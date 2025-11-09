<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\File;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class FileUploadService
{
    public const ALLOWED_END_PONTS = [
        'questions',
        'answers',
    ];

    /**
     * @throws RouteNotFoundException|\Throwable
     */
    public function validateEndPoint(string $type, string $id): Model
    {
        throw_unless(
            in_array($type, self::ALLOWED_END_PONTS),
            NotFoundHttpException::class
        );

        return $this->getModelInstance($type, $id);
    }

    public function checkAuthorized(string $type): void
    {
        switch ($type) {
            case 'questions':
            case 'answers':
                Gate::authorize('administrative');
                break;
            default:
                abort(404);
        }
    }

    /**
     * @return mixed[]
     */
    public function validateUploadedFiles(Request $request, string $type): array
    {
        switch ($type) {
            case 'questions':
            case 'answers':
                return $request->validate([
                    'images' => ['array', 'required'],
                    'images.*' => ['required', File::image()->max(5 * 1024)],
                ])['images'];
        }

        return [];
    }

    /**
     * @param  array  $files
     */
    public function execute(Model $model, $files, string $collecton = 'default'): ?MediaCollection
    {
        $modelName = class_basename($model);

        switch ($modelName) {
            case 'Question':
            case 'Answer':
                return $this->multipleFileUpload($files, $model, $collecton);
        }

        return null;
    }

    /*public function singleFileUpload()
    {
    }*/

    /**
     * @param array $medias
     * @throws \Exception
     */
    public function multipleFileUpload($medias, Model $model, string $collection): MediaCollection
    {
        foreach ($medias as $media) {
            // @phpstan-ignore-next-line

            try {
                $model->addMedia($media)
                    ->toMediaCollection($collection);
            }catch (\Exception $exception){
                if (config('app.symblink_support')) {
                    throw $exception;
                }
            }

        }

        // @phpstan-ignore-next-line
        return $model->getMedia();
    }

    public function getAllFiles(string $type, string $modelId): MediaCollection
    {
        // @phpstan-ignore-next-line
        return $this->getModelInstance($type, $modelId)->getMedia();
    }

    public function deleteFile(string $type, string $modelId, int $deletableImageId): void
    {
        $model = $this->getModelInstance($type, $modelId);

        // @phpstan-ignore-next-line
        $imageExists = $model->media()->whereId($deletableImageId)->exists();

        if ($imageExists) {
            $image = Media::query()->findOrFail($deletableImageId);

            $image->delete();
        }
    }

    public function validateDeletableFiles(Request $request): array
    {
        return $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'string'],
        ])['ids'];
    }

    public function massDeleteFiles(array $ids): void
    {
        collect($ids)->each(function ($id) {
            Media::query()
                ->where('uuid', $id)
                ->firstOrFail()
                ->delete();
        });
    }

    public function validateToChangeOrder(Request $request): array
    {
        return $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer'],
        ])['order'];
    }

    /**
     * @param  string[]  $ids
     */
    public function changeOrder(array $ids): void
    {
        collect($ids)->each(function (int $order, string $key) {
            Media::query()
                ->where('uuid', $key)
                ->update(['order_column' => $order]);
        });
    }

    public function getModelInstance(string $type, string $id): Model
    {
        $hashId = new Hashids;

        return match ($type) {
            'questions' => app(Question::class)::findOrFail($hashId->decode($id)[0]),
            'answers' => app(Answer::class)::findOrFail($hashId->decode($id)[0]),
        };
    }
}

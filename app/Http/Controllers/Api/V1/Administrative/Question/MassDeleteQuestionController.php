<?php

namespace App\Http\Controllers\Api\V1\Administrative\Question;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class MassDeleteQuestionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  UserStoreRequest  $request
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validatedInputs = $request->validate([
            'ids' => ['array', 'required'],
            'ids.*' => ['string', 'required'],
        ]);

        collect($validatedInputs['ids'])->each(function (string $id) {
            Question::find(Hashids::decode($id)[0])?->delete();
        });

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\SetModelPrettyId;
use App\Models\Concerns\HasHashids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Answer
 *
 * @property int $id
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\AnswerFactory factory(...$parameters)
 * @method static Builder|Answer newModelQuery()
 * @method static Builder|Answer newQuery()
 * @method static Builder|Answer query()
 * @method static Builder|Answer whereCreatedAt($value)
 * @method static Builder|Answer whereId($value)
 * @method static Builder|Answer whereText($value)
 * @method static Builder|Answer whereUpdatedAt($value)
 *
 * @property string $pretty_id
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $images
 * @property-read int|null $images_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read mixed $hash_id
 *
 * @method static Builder<static>|Answer wherePrettyId($value)
 *
 * @mixin \Eloquent
 */
class Answer extends Model implements HasMedia
{
    use HasFactory;
    use HasHashids;
    use InteractsWithMedia;

    protected $fillable = [
        'text',
        'pretty_id',
    ];

    protected $dispatchesEvents = [
        'creating' => SetModelPrettyId::class,
    ];

    // --------------------------Relationships----------------------------

    /**
     * @return MorphMany<Answer>
     */
    public function images(): MorphMany
    {
        return $this->media()->orderBy('order_column');
    }

    // ------------------------End Of Relationships----------------------------
}

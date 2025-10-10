<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasHashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Questionnaire[] $questionnaires
 * @property-read int|null $questionnaires_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Question[] $questions
 * @property-read int|null $questions_count
 *
 * @method static \Database\Factories\CategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 *
 * @property-read mixed $hash_id
 *
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;
    use HasHashids;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'pivot',
    ];

    // --------------------------Relationships----------------------------

    /**
     * @return MorphToMany<Questionnaire>
     */
    public function questionnaires(): MorphToMany
    {
        return $this->morphedByMany(
            related: Questionnaire::class,
            name: 'categorizable',
            table: 'categorizables',
            foreignPivotKey: 'category_id'
        );
    }

    /**
     * @return MorphToMany<Question>
     */
    public function questions(): MorphToMany
    {
        return $this->morphedByMany(
            related: Question::class,
            name: 'categorizable',
            table: 'categorizables',
            foreignPivotKey: 'category_id'
        );
    }

    // -------------------------End of Relationships----------------------------
}

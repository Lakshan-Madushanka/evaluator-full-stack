<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Difficulty;
use App\Events\SetModelPrettyId;
use App\Models\Concerns\HasHashids;
use Database\Factories\QuestionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Question
 *
 * @property int $id
 * @property Difficulty $difficulty
 * @property string $text
 * @property int $no_of_answers
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Answer[] $answers
 * @property-read int|null $answers_count
 * @property-read Collection|Category[] $categories
 * @property-read int|null $categories_count
 *
 * @method static QuestionFactory factory(...$parameters)
 * @method static Builder|Question newModelQuery()
 * @method static Builder|Question newQuery()
 * @method static Builder|Question query()
 * @method static Builder|Question whereCreatedAt($value)
 * @method static Builder|Question whereDifficulty($value)
 * @method static Builder|Question whereId($value)
 * @method static Builder|Question whereNoOfAnswers($value)
 * @method static Builder|Question whereText($value)
 * @method static Builder|Question whereUpdatedAt($value)
 *
 * @property-read MediaCollection|Media[] $images
 * @property-read int|null $images_count
 * @property-read MediaCollection|Media[] $media
 * @property-read int|null $media_count
 *
 * @method static Builder|Question completed()
 *
 * @property string $pretty_id
 * @property bool $is_answers_type_single
 * @property-read Collection|\App\Models\Answer[] $onlyAnswers
 * @property-read int|null $only_answers_count
 *
 * @method static Builder|Question eligible(\App\Models\Questionnaire $questionnaire)
 * @method static Builder|Question whereIsAnswersTypeSingle($value)
 * @method static Builder|Question wherePrettyId($value)
 *
 * @property-read mixed $hash_id
 *
 * @mixin Eloquent
 */
class Question extends Model implements HasMedia
{
    use HasFactory;
    use HasHashids;
    use InteractsWithMedia;

    /**
     * @var string[]
     */
    protected $fillable = [
        'difficulty',
        'text',
        'no_of_answers',
        'is_answers_type_single',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'difficulty' => Difficulty::class,
        'is_answers_type_single' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'creating' => SetModelPrettyId::class,
    ];

    // --------------------------Relationships----------------------------

    /**
     * @return MorphToMany<Category>
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(
            related: Category::class,
            name: 'categorizable',
            table: 'categorizables',
            relatedPivotKey: 'category_id'
        );
    }

    /**
     * @return BelongsToMany<Answer>
     */
    public function answers(): BelongsToMany
    {
        return $this->belongsToMany(
            Answer::class,
            'question_answer',
            foreignPivotKey: 'question_id',
            relatedPivotKey: 'answer_id'
        )->withPivot(['correct_answer']);
    }

    /**
     * @return BelongsToMany<Answer>
     */
    public function onlyAnswers(): BelongsToMany
    {
        return $this->belongsToMany(
            Answer::class,
            'question_answer',
            foreignPivotKey: 'question_id',
            relatedPivotKey: 'answer_id'
        );
    }

    /**
     * @return MorphMany<Model>
     */
    public function images(): MorphMany
    {
        return $this->media()->orderBy('order_column');
    }

    // -------------------------End of Relationships----------------------------

    // --------------------------------Query scopes-----------------------------

    public function scopeCompleted(Builder $query): Builder
    {
        $table = $this->getTable();

        return $query->withCount('answers')
            ->when(config('app.older_db_version_support'), function (Builder $query) use ($table) {
                $query
                    ->whereRaw("(select count(*) from question_answer where question_answer.question_id = {$table}.id) = {$table}.no_of_answers");
            })
            ->when(! config('app.older_db_version_support'), function (Builder $query) {
                $query->havingRaw('no_of_answers = answers_count');
            });
    }

    // Eligible question to assign for a questionnaire
    public function scopeEligible(Builder $query, Questionnaire $questionnaire): Builder
    {
        $questionnaireCategoriesIds = $questionnaire->categories()->pluck('categories.id');

        return $query->completed()
            ->when($questionnaire->single_answers_type, function (Builder $query) {
                $query->where('is_answers_type_single', true);
            })
            ->whereHas('categories',
                fn (Builder $query) => $query->whereIn('categories.id', $questionnaireCategoriesIds));
    }

    // --------------------------------End of scopes-----------------------------

    public function checkQuestionIsComplete(Question $question): bool
    {
        if (! $this->hasAttribute('answers_count')) {
            $question->loadCount('answers');
        }

        return $question->no_of_answers === $question->answers_count;
    }
}

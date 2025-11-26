<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Difficulty;
use App\Helpers;
use App\Models\Concerns\HasHashids;
use Database\Factories\QuestionnaireFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Questionnaire
 *
 * @property int $id
 * @property string $content
 * @property Difficulty $difficulty
 * @property int $no_of_questions
 * @property int $no_of_easy_questions
 * @property int $no_of_medium_questions
 * @property int $no_of_hard_questions
 * @property int $allocated_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Category[] $categories
 * @property-read int|null $categories_count
 * @property-read UserQuestionnaire|null $evaluations
 * @property-read Collection|Question[] $questions
 * @property-read int|null $questions_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 *
 * @method static QuestionnaireFactory factory(...$parameters)
 * @method static Builder|Questionnaire newModelQuery()
 * @method static Builder|Questionnaire newQuery()
 * @method static Builder|Questionnaire query()
 * @method static Builder|Questionnaire whereAllocatedTime($value)
 * @method static Builder|Questionnaire whereContent($value)
 * @method static Builder|Questionnaire whereCreatedAt($value)
 * @method static Builder|Questionnaire whereDifficulty($value)
 * @method static Builder|Questionnaire whereId($value)
 * @method static Builder|Questionnaire whereNoOfEasyQuestions($value)
 * @method static Builder|Questionnaire whereNoOfHardQuestions($value)
 * @method static Builder|Questionnaire whereNoOfMediumQuestions($value)
 * @method static Builder|Questionnaire whereNoOfQuestions($value)
 * @method static Builder|Questionnaire whereUpdatedAt($value)
 *
 * @property string $name
 *
 * @method static Builder|Questionnaire whereName($value)
 *
 * @property bool $single_answers_type
 * @property-read Collection|\App\Models\Question[] $questionsWithPivotData
 * @property-read int|null $questions_with_pivot_data_count
 *
 * @method static Builder|Questionnaire completed($value)
 * @method static Builder|Questionnaire whereSingleAnswersType($value)
 *
 * @property-read mixed $hash_id
 *
 * @mixin Eloquent
 */
class Questionnaire extends Model
{
    use HasFactory;
    use HasHashids;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'difficulty',
        'single_answers_type',
        'no_of_questions',
        'no_of_easy_questions',
        'no_of_medium_questions',
        'no_of_hard_questions',
        'allocated_time',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'difficulty' => Difficulty::class,
        'single_answers_type' => 'boolean',
        'expires_at' => 'datetime',
    ];

    // --------------------------------Scopes--------------------------------------------
    public function scopeCompleted(Builder $query, $value): Builder
    {
        $table = $this->getTable();

        $sub = "(select count(*) from questionnaire_question where questionnaire_question.questionnaire_id = {$table}.id)";

        if (Helpers::checkValueIsTrue($value)) {
            return $query
                ->when(config('app.older_db_version_support'), function (Builder $query) use ($sub, $table) {
                    return $query->whereRaw("{$sub} = {$table}.no_of_questions");
                })
                ->when(!config('app.older_db_version_support'), function (Builder $query) use ($sub, $table) {
                    return $query->havingRaw('questions_count = no_of_questions');
                });
        }

        return $query
            ->when(config('app.older_db_version_support'), function (Builder $query) use ($sub, $table) {
                return $query->whereRaw("{$sub} <> {$table}.no_of_questions");
            })
            ->when(!config('app.older_db_version_support'), function (Builder $query) use ($sub, $table) {
                return $query->havingRaw('questions_count <> no_of_questions');
            });
    }

    // --------------------------------Scopes--------------------------------------------

    // --------------------------Relationships--------------------------------------------
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
     * @return BelongsToMany<Question>
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Question::class,
            table: 'questionnaire_question',
            foreignPivotKey: 'questionnaire_id',
            relatedPivotKey: 'question_id'
        );
    }

    /**
     * @return BelongsToMany<Question>
     */
    public function questionsWithPivotData(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Question::class,
            table: 'questionnaire_question',
            foreignPivotKey: 'questionnaire_id',
            relatedPivotKey: 'question_id'
        )->withPivot(['marks']);
    }

    /**
     * @return BelongsToMany<User>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'user_questionnaire',
            foreignPivotKey: 'questionnaire_id',
            relatedPivotKey: 'user_id',
        );
    }
    // -------------------------End of Relationships----------------------------
}

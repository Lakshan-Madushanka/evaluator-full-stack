<?php

namespace App\Models;

use App\Models\Concerns\HasHashids;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;

/**
 * App\Models\Evaluation
 *
 * @property int $id
 * @property int $user_questionnaire_id
 * @property int $time_taken
 * @property int $correct_answers
 * @property int $no_of_answered_questions
 * @property int $marks
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Evaluation newModelQuery()
 * @method static Builder|Evaluation newQuery()
 * @method static Builder|Evaluation query()
 * @method static Builder|Evaluation whereCorrectAnswers($value)
 * @method static Builder|Evaluation whereCreatedAt($value)
 * @method static Builder|Evaluation whereId($value)
 * @method static Builder|Evaluation whereMarks($value)
 * @method static Builder|Evaluation whereNoOfAnsweredQuestions($value)
 * @method static Builder|Evaluation whereTimeTaken($value)
 * @method static Builder|Evaluation whereUpdatedAt($value)
 * @method static Builder|Evaluation whereUserQuestionnaireId($value)
 *
 * @property float|null $marks_percentage
 * @property float|null $total_points_earned
 * @property float|null $total_points_allocated
 * @property-read mixed $hash_id
 * @property-read \App\Models\Questionnaire|null $questionnaire
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\UserQuestionnaire $userQuestionnaire
 *
 * @method static Builder<static>|Evaluation filterByQuestionnaireId(string $value)
 * @method static Builder<static>|Evaluation filterByUserId(string $value)
 * @method static Builder<static>|Evaluation filterByUserQuestionnaire(...$value)
 * @method static Builder<static>|Evaluation whereMarksPercentage($value)
 * @method static Builder<static>|Evaluation whereTotalPointsAllocated($value)
 * @method static Builder<static>|Evaluation whereTotalPointsEarned($value)
 *
 * @property int $questionnaireable_id
 *
 * @method static Builder<static>|Evaluation whereQuestionnaireableId($value)
 *
 * @mixin Eloquent
 */
class Evaluation extends Model
{
    use HasFactory;
    use HasHashids;
    use HasHashids;

    protected $fillable = [
        'user_questionnaire_id',
        'time_taken',
        'correct_answers',
        'no_of_answered_questions',
        'marks_percentage',
        'total_points_earned',
        'total_points_allocated',
    ];

    // ----------------------------------------Scopes-----------------------------------------------
    public function scopeFilterByUserId(Builder $query, string $value): Builder
    {
        return $query->whereHas('userQuestionnaire', function (Builder $builder) use ($value) {
            $builder->where('user_id', Hashids::decode($value));
        });
    }

    public function scopeFilterByQuestionnaireId(Builder $query, string $value): Builder
    {
        return $query->whereHas('userQuestionnaire', function (Builder $builder) use ($value) {
            $builder->where('questionnaire_id', Hashids::decode($value));
        });
    }

    public function scopeFilterByUserQuestionnaire(Builder $query, ...$value): Builder
    {
        return $query->whereHas('userQuestionnaire', function (Builder $builder) use ($value) {
            $builder->where(
                [
                    [
                        'user_id', Hashids::decode($value[0]),
                    ],
                    [
                        'questionnaire_id', Hashids::decode($value[1]),
                    ],
                ]);

        });
    }

    // ----------------------------------------Scopes-----------------------------------------------

    // -------------------------------- Relationships-----------------------------------------------
    public function userQuestionnaire(): BelongsTo
    {
        return $this->belongsTo(UserQuestionnaire::class, 'user_questionnaire_id');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            UserQuestionnaire::class,
            'id',
            'id',
            'user_questionnaire_id',
            'user_id',
        );
    }

    public function questionnaire()
    {
        return $this->hasOneThrough(
            Questionnaire::class,
            UserQuestionnaire::class,
            'id',
            'id',
            'user_questionnaire_id',
            'questionnaire_id',
        );
    }
    // --------------------------End of Relationships-----------------------------------------------
}

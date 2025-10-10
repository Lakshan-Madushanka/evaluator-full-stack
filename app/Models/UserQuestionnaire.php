<?php

namespace App\Models;

use App\Models\Concerns\HasHashids;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\UserQuestionnaire
 *
 * @property int $id
 * @property int $questionnaire_id
 * @property int $user_id
 * @property int $attempts
 * @property string|null $code
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property \Illuminate\Support\Carbon $expires_at
 * @property array|null $answers
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Evaluation|null $evaluation
 *
 * @method static Builder|UserQuestionnaire available(string $code)
 * @method static Builder|UserQuestionnaire checkAvailable()
 * @method static Builder|UserQuestionnaire expired($expired = false)
 * @method static Builder|UserQuestionnaire newModelQuery()
 * @method static Builder|UserQuestionnaire newQuery()
 * @method static Builder|UserQuestionnaire query()
 * @method static Builder|UserQuestionnaire whereAnswers($value)
 * @method static Builder|UserQuestionnaire whereAttempts($value)
 * @method static Builder|UserQuestionnaire whereCode($value)
 * @method static Builder|UserQuestionnaire whereCreatedAt($value)
 * @method static Builder|UserQuestionnaire whereExpiresAt($value)
 * @method static Builder|UserQuestionnaire whereFinishedAt($value)
 * @method static Builder|UserQuestionnaire whereId($value)
 * @method static Builder|UserQuestionnaire whereQuestionnaireId($value)
 * @method static Builder|UserQuestionnaire whereStartedAt($value)
 * @method static Builder|UserQuestionnaire whereUpdatedAt($value)
 * @method static Builder|UserQuestionnaire whereUserId($value)
 *
 * @property-read mixed $hash_id
 * @property int|null $questionnaire_team_id
 *
 * @method static Builder<static>|UserQuestionnaire whereQuestionnaireTeamId($value)
 *
 * @mixin Eloquent
 */
class UserQuestionnaire extends Model
{
    use HasHashids;

    protected $table = 'user_questionnaire';

    /**
     * @var string[]
     */
    protected $fillable = [
        'questionnaire_team_id',
        'questionnaire_id',
        'expires_at',
        'started_at',
        'user_id',
        'code',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'expires_at' => 'datetime',
        'answers' => 'array',
    ];

    // -----------------------------------scopes-------------------------------------------------------------------------

    public function scopeAvailable(Builder $query, string $code): Builder
    {
        return $query
            ->where('code', $code)
            ->where('attempts', 0)
            ->where('expires_at', '>=', now());
    }

    public function scopeCheckAvailable(Builder $query): Builder
    {
        return $query
            ->where('attempts', 0)
            ->where('expires_at', '>=', now());
    }

    public function scopeExpired(Builder $query, $expired = false): Builder
    {
        if ($expired) {
            return $query
                ->where('expires_at', '<=', now());
        }

        return $query
            ->where('expires_at', '>=', now());
    }

    // -----------------------------------scopes-------------------------------------------------------------------------

    // --------------------------Relationships----------------------------

    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class, foreignKey: 'user_questionnaire_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------End of Relationships----------------------------
}

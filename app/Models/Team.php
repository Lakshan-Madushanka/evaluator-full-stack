<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasHashids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property-read mixed $hash_id
 *
 * @method static Builder<static>|Team newModelQuery()
 * @method static Builder<static>|Team newQuery()
 * @method static Builder<static>|Team query()
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\TeamFactory factory($count = null, $state = [])
 * @method static Builder<static>|Team whereCreatedAt($value)
 * @method static Builder<static>|Team whereId($value)
 * @method static Builder<static>|Team whereName($value)
 * @method static Builder<static>|Team whereUpdatedAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Questionnaire> $questionnaires
 * @property-read int|null $questionnaires_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserQuestionnaire> $userQuestionnaires
 * @property-read int|null $user_questionnaires_count
 *
 * @mixin \Eloquent
 */
class Team extends Model
{
    use HasFactory, HasHashids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    // --------------------------Relationships----------------------------

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function questionnaires(): BelongsToMany
    {
        return $this
            ->belongsToMany(Questionnaire::class)
            ->withPivot(['id'])
            ->withTimestamps();
    }

    public function userQuestionnaires(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                UserQuestionnaire::class,
                QuestionnaireTeam::class,
                'team_id',
                'questionnaire_team_id',
            );
    }

    // -------------------------End of Relationships----------------------------
}

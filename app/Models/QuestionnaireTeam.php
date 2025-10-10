<?php

namespace App\Models;

use App\Models\Concerns\HasHashids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read mixed $hash_id
 *
 * @method static Builder<static>|QuestionnaireTeam newModelQuery()
 * @method static Builder<static>|QuestionnaireTeam newQuery()
 * @method static Builder<static>|QuestionnaireTeam query()
 *
 * @property int $id
 * @property int|null $questionnaire_id
 * @property int|null $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserQuestionnaire> $users
 * @property-read int|null $users_count
 *
 * @method static Builder<static>|QuestionnaireTeam whereCreatedAt($value)
 * @method static Builder<static>|QuestionnaireTeam whereId($value)
 * @method static Builder<static>|QuestionnaireTeam whereQuestionnaireId($value)
 * @method static Builder<static>|QuestionnaireTeam whereTeamId($value)
 * @method static Builder<static>|QuestionnaireTeam whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class QuestionnaireTeam extends Model
{
    use HasHashids;

    protected $table = 'questionnaire_team';

    /**
     * @var string[]
     */
    protected $fillable = [
        'questionnaire_id',
        'team_id',
    ];

    // --------------------------Relationships----------------------------

    public function users(): HasMany
    {
        return $this->hasMany(UserQuestionnaire::class, 'questionnaire_team_id');
    }

    // -------------------------End of Relationships----------------------------
}

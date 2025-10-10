<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use App\Models\Concerns\HasHashids;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property Role $role
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Questionnaire[] $questionnaires
 * @property-read int|null $questionnaires_count
 * @property-read Collection|Questionnaire[] $questionnairesWithAnswers
 * @property-read int|null $questionnaires_with_answers_count
 *
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRole($value)
 * @method static Builder|User whereUpdatedAt($value)
 *
 * @property-read Collection|Questionnaire[] $questionnairesWithPivotData
 * @property-read int|null $questionnaires_with_pivot_data_count
 * @property-read Collection<int, \App\Models\Evaluation> $evaluations
 * @property-read int|null $evaluations_count
 * @property-read mixed $hash_id
 * @property-read Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 *
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasHashids, Notifiable;

    /**
     * @var string[]
     */
    protected $appends = [
        'hash_id',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => Role::class,
    ];

    // --------------------------Relationships----------------------------

    /**
     * @return BelongsToMany<Questionnaire>
     */
    public function questionnaires(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Questionnaire::class,
            table: 'user_questionnaire',
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'questionnaire_id'
        )
            ->withPivot('questionnaire_team_id')
            ->withTimestamps();
    }

    public function questionnairesWithPivotData(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Questionnaire::class,
            table: 'user_questionnaire',
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'questionnaire_id'
        )->withPivot(['answers'])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Questionnaire>
     */
    public function questionnairesWithAnswers(): BelongsToMany
    {
        return $this->questionnaires()->withPivotValue(['answers']);
    }

    public function evaluations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Evaluation::class,
            UserQuestionnaire::class,
            'user_id',
            'user_questionnaire_id',
        );
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }
    // -------------------------End of Relationships----------------------------
}

<?php

namespace Tests\Repositories;

use App\Models\Questionnaire;
use App\Models\QuestionnaireTeam;
use App\Models\Team;
use App\Models\User;
use App\Models\UserQuestionnaire;
use App\Services\QuestionnaireService;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository
{
    public static function getRandomTeam(): Team
    {
        return Team::inRandomOrder()->first();
    }

    public static function getTotalTeamsCount(): int
    {
        return Team::count();
    }

    public static function getRandomTeams(int $limit = 10): Collection
    {
        return Team::inRandomOrder()->limit($limit)->get();
    }

    public static function createTeamsWithQuestionnaires($noOfUsers = 10): Team
    {
        $questionnaire = Questionnaire::query()
            ->withCount('questions')
            ->completed(value: true)
            ->first();

        $team = Team::factory()
            ->has(User::factory()->count($noOfUsers))
            ->create();

        $team->questionnaires()->attach($questionnaire);

        $teamQuestionnaire = QuestionnaireTeam::orderByDesc('id')->first();

        $users = $team->users;

        $questionnaireService = app(QuestionnaireService::class);

        foreach ($users as $user) {
            ['code' => $code, 'expires_at' => $expiresAt] = $questionnaireService->getAttributes($questionnaire);

            UserQuestionnaire::create([
                'questionnaire_team_id' => $teamQuestionnaire->id,
                'user_id' => $user->id,
                'questionnaire_id' => $questionnaire->id,
                'code' => $code,
                'expires_at' => $expiresAt,
            ]);
        }

        return $team;
    }
}

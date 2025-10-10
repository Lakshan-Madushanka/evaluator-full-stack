<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(50)->create();
        $this->assignQuestionnaires($users);
    }

    public function assignQuestionnaires(Collection $users): void
    {
        $users->each(function (User $user) {
            $data = [];
            $questionnaires = Questionnaire::query()
                ->withCount('questions')
                ->completed(true)
                ->inRandomOrder()
                ->limit(random_int(1, 5))
                ->get()
                ->each(function (Questionnaire $questionnaire) use (&$data) {
                    $data[$questionnaire->id] = [
                        'code' => Str::uuid(),
                        'expires_at' => now()->addMinutes($questionnaire->allocated_time * 2),
                    ];
                });

            $user->questionnaires()->sync($data);
        });
    }
}

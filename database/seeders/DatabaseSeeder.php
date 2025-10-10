<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Database\Data\Data;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! App::isProduction()) {
            $this->createAdmin();
            $this->createSuperAdmin();
        }

        // Don't change the order
        if (App::environment('testing')) {
            $this->call([
                CategorySeeder::class,
                QuestionnaireSeeder::class,
                QuestionSeeder::class,
                AnswerSeeder::class,
                UserSeeder::class,
                TeamSeeder::class,
            ]);
        }

        if (App::environment('local')) {
            Data::seedQuestions();

            $this->call([
                TeamSeeder::class,
            ]);
        }
    }

    public function createAdmin(): void
    {
        $adminEmail = 'admin@company.com';

        User::whereEmail($adminEmail)->existsOr(function () {
            User::factory()->create([
                'role' => Role::ADMIN,
                'password' => Hash::make('admin123'),
                'email' => 'admin@company.com',
            ]);
        });
    }

    public function createSuperAdmin(): void
    {
        $superAdminEmail = 'super-admin@company.com';

        User::whereEmail($superAdminEmail)->existsOr(function () {
            User::factory()->create([
                'role' => Role::SUPER_ADMIN,
                'password' => Hash::make('superAdmin123'),
                'email' => 'super-admin@company.com',
            ]);
        });
    }
}

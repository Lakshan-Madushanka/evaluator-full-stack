<?php

declare(strict_types=1);

namespace Database\Data;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserData {

    /**
     * @var array<int, array{name:string, email:string, role:mixed}>
     */
    public static $users = [
        [
            'name' => 'John Doe',
            'email' => 'johndoe@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Jane Smith',
            'email' => 'janesmith@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Alice Johnson',
            'email' => 'alicejohnson@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Bob Brown',
            'email' => 'bobbrown@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Carol Davis',
            'email' => 'caroldavis@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'David Wilson',
            'email' => 'davidwilson@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Emily Clark',
            'email' => 'emilyclark@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Frank Lewis',
            'email' => 'franklewis@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Grace Hall',
            'email' => 'gracehall@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Henry Young',
            'email' => 'henryyoung@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Irene King',
            'email' => 'ireneking@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Jack Wright',
            'email' => 'jackwright@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Karen Turner',
            'email' => 'karenturner@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Larry Scott',
            'email' => 'larryscott@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
        [
            'name' => 'Monica Hill',
            'email' => 'monicahill@testmail.com',
            'role' => Role::REGULAR,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ],
    ];

    public static function seedUsers(): void
    {
        $team = Team::create(['name' => 'Team Alpha']);

        DB::table((new User())->getTable())->insert(self::$users);

        $team->users()->sync(User::query()->where('role', Role::REGULAR)->limit(5)->get());
    }
}

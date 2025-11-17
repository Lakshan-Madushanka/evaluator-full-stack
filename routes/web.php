<?php

use App\Enums\Role;
use App\Models\User;
use Database\Data\GNData;
use Database\Data\IQData;
use Database\Data\IQPatternsData;
use Database\Data\ProgrammingData;
use Database\Data\UserData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', function () {
    $status = \Illuminate\Support\Facades\Artisan::call('optimize:clear');

    Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
    UserData::seedUsers();
    ProgrammingData::seedQuestions();
    IQData::seedQuestions();
    GNData::seedQuestions();
    IQPatternsData::seedQuestions();

    $superAdminEmail = 'super-admin@company.com';

    User::whereEmail($superAdminEmail)->existsOr(function () {
        User::create([
            'name' => 'Super Admin',
            'role' => Role::SUPER_ADMIN,
            'password' => Hash::make('superAdmin123'),
            'email' => 'super-admin@company.com',
        ]);
    });
    return 'ok....status ' . $status;
});

Route::get('/', function () {
    return view('index');
});

Route::get('/{any}', function () {
    return view('index'); // or your main blade file that loads Vue
})->where('any', '^(?!api)(?!upload).*$');

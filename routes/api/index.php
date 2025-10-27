<?php

use App\Http\Controllers\Api\Setup\CheckDBController;
use App\Http\Controllers\Api\Setup\CheckEnvController;
use App\Http\Controllers\Api\Setup\CheckFilePermissionsController;
use App\Http\Controllers\Api\Setup\CheckPHPRequirementsController;
use App\Http\Controllers\Api\Setup\CheckSuperAdminAccountController;
use App\Http\Controllers\Api\Setup\OptimizeController;
use App\Http\Middleware\SetupMiddleware;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Cache;

Route::name('setup.')->prefix('setup')->group(function () {
    Route::get('check-status', function () {
        return response()->json(['status' => Cache::get('setup_status', 'incompleted')]);
    })->name('check-status');

    Route::middleware([SetupMiddleware::class])->group(function () {
        /**
         * PHP Requirements Check
         */
        Route::get('/check-php-version' , [CheckPHPRequirementsController::class, 'checkPHPVersion'])->name('check-php-version');
        Route::get('/check-php-extensions' , [CheckPHPRequirementsController::class, 'checkExtensions'])->name('check-php-extensions');

        /**
         * File Permissions Check
         */
        Route::get('/check-file-permissions', CheckFilePermissionsController::class)->name('check-file-permissions');

        /**
         * Env Check
         */
        Route::get('/check-env', [CheckEnvController::class, 'check'])->name('check-env');
        Route::get('/generate-key', [CheckEnvController::class, 'generateKey'])->name('generate-key');

        /**
         * DB Check
         */
        Route::get('db/get-info', [CheckDBController::class, 'info'])->name('db.get-info');
        Route::get('db/check-connection', [CheckDBController::class, 'checkConnection'])->name('db.check-connection');
        Route::get('db/migrate', [CheckDBController::class, 'migrate'])->name('db.migrate');

        /**
         * Account
         */
        Route::get('account/check-exists', [CheckSuperAdminAccountController::class, 'checkExists'])->name('account.check-exists');
        Route::post('account/create', [CheckSuperAdminAccountController::class, 'create'])->name('account.create');

        /**
         * Optimize
         */
        Route::post('optimize', OptimizeController::class)->name('optimize');
    });
});

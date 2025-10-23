<?php

use App\Http\Controllers\Api\Setup\CheckDBController;
use App\Http\Controllers\Api\Setup\CheckEnvController;
use App\Http\Controllers\Api\Setup\CheckFilePermissionsController;
use App\Http\Controllers\Api\Setup\CheckPHPRequirementsController;
use Illuminate\Support\Facades\Route;

Route::name('setup.')->prefix('setup')->group(function () {
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
    Route::get('/check-env', CheckEnvController::class)->name('check-env');

    /**
     * DB Check
     */
    Route::get('db/get-info', [CheckDBController::class, 'info'])->name('db.get-info');
    Route::get('db/check-connection', [CheckDBController::class, 'checkConnection'])->name('db.check-connection');
    Route::get('db/migrate', [CheckDBController::class, 'migrate'])->name('db.migrate');
});

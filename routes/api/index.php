<?php

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
});

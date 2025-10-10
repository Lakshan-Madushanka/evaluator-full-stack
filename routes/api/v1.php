<?php

use App\Http\Controllers\Api\V1\Administrative\Answer\CheckAnswerExistsController;
use App\Http\Controllers\Api\V1\Administrative\Answer\DeleteAnswerController;
use App\Http\Controllers\Api\V1\Administrative\Answer\IndexAnswerController;
use App\Http\Controllers\Api\V1\Administrative\Answer\MassDeleteAnswerController;
use App\Http\Controllers\Api\V1\Administrative\Answer\ShowAnswerController;
use App\Http\Controllers\Api\V1\Administrative\Answer\StoreAnswerController;
use App\Http\Controllers\Api\V1\Administrative\Answer\UpdateAnswerController;
use App\Http\Controllers\Api\V1\Administrative\Auth\LogInController;
use App\Http\Controllers\Api\V1\Administrative\Auth\LogOutController;
use App\Http\Controllers\Api\V1\Administrative\Auth\ShowAuthUserController;
use App\Http\Controllers\Api\V1\Administrative\Category\DeleteCategoryController;
use App\Http\Controllers\Api\V1\Administrative\Category\IndexCategoryController;
use App\Http\Controllers\Api\V1\Administrative\Category\ShowCategoryController;
use App\Http\Controllers\Api\V1\Administrative\Category\StoreCategoryController;
use App\Http\Controllers\Api\V1\Administrative\Category\UpdateCategoryController;
use App\Http\Controllers\Api\V1\Administrative\Dashboard\IndexDashboardController;
use App\Http\Controllers\Api\V1\Administrative\Dashboard\QuestionController;
use App\Http\Controllers\Api\V1\Administrative\Dashboard\QuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Evaluation\IndexEvaluationController;
use App\Http\Controllers\Api\V1\Administrative\Evaluation\ShowEvaluationController;
use App\Http\Controllers\Api\V1\Administrative\Profile\UpdateProfileController;
use App\Http\Controllers\Api\V1\Administrative\Question\Answer\AsyncAnswerController;
use App\Http\Controllers\Api\V1\Administrative\Question\DeleteQuestionController;
use App\Http\Controllers\Api\V1\Administrative\Question\IndexQuestionController;
use App\Http\Controllers\Api\V1\Administrative\Question\MassDeleteQuestionController;
use App\Http\Controllers\Api\V1\Administrative\Question\ShowQuestionController;
use App\Http\Controllers\Api\V1\Administrative\Question\StoreQuestionController;
use App\Http\Controllers\Api\V1\Administrative\Question\UpdateQuestionController;
use App\Http\Controllers\Api\V1\Administrative\Questionnaire\DeleteQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Questionnaire\IndexQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Questionnaire\MassDeleteQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Questionnaire\Question\EligibleQuestionController;
use App\Http\Controllers\Api\V1\Administrative\Questionnaire\Question\SyncQuestionController;
use App\Http\Controllers\Api\V1\Administrative\Questionnaire\ShowQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Questionnaire\StoreQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Questionnaire\UpdateQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Team\DeleteTeamController;
use App\Http\Controllers\Api\V1\Administrative\Team\IndexTeamController;
use App\Http\Controllers\Api\V1\Administrative\Team\Questionnaire\AttachQuestionnaireController as AttachTeamQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Team\Questionnaire\DetachQuestionnaireController as DetachTeamQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Team\Questionnaire\IndexQuestionnaireController as IndexTeamQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\Team\Questionnaire\User\IndexUserController as IndexTeamQuestionnaireUserController;
use App\Http\Controllers\Api\V1\Administrative\Team\ShowTeamController;
use App\Http\Controllers\Api\V1\Administrative\Team\StoreTeamController;
use App\Http\Controllers\Api\V1\Administrative\Team\UpdateTeamController;
use App\Http\Controllers\Api\V1\Administrative\Team\User\DetachUserController;
use App\Http\Controllers\Api\V1\Administrative\Team\User\IndexUserController as TeamUserIndexController;
use App\Http\Controllers\Api\V1\Administrative\User\IndexUserController;
use App\Http\Controllers\Api\V1\Administrative\User\Questionnaire\AttachQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\User\Questionnaire\DetachQuestionnaireController;
use App\Http\Controllers\Api\V1\Administrative\User\Questionnaire\IndexQuestionnaireController as UserQuestionnaireIndexController;
use App\Http\Controllers\Api\V1\Administrative\User\Questionnaire\ResendQuestionnaireAttachedNotificationController;
use App\Http\Controllers\Api\V1\Administrative\User\ShowUserController;
use App\Http\Controllers\Api\V1\Administrative\User\Team\AttachTeamController;
use App\Http\Controllers\Api\V1\Administrative\User\Team\IndexTeamController as IndexUserTeamControllerAlias;
use App\Http\Controllers\Api\V1\FileUploadController;
use App\Http\Controllers\Api\V1\Regular\User\Questionnaire\CheckQuestionnaireAvailableController;
use App\Http\Controllers\Api\V1\Regular\User\Questionnaire\EvaluateQuestionnaireController;
use App\Http\Controllers\Api\V1\SuperAdmin\User\CreateUserController;
use App\Http\Controllers\Api\V1\SuperAdmin\User\DeleteUserController;
use App\Http\Controllers\Api\V1\SuperAdmin\User\MassDeleteUserController;
use App\Http\Controllers\Api\V1\SuperAdmin\User\UpdateUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Illuminate\Support\Facades\Auth::loginUsingId(2);
Route::get('/test', function (Request $request) {});

Route::prefix('super-admin')->name('super-admin.')->group(function () {
    /**
     * Users
     */
    Route::middleware(['auth:sanctum', 'can:super-admin'])
        ->post('/users', CreateUserController::class)
        ->name('users.store');

    Route::middleware(['auth:sanctum', 'can:super-admin'])
        ->put('/users/{user}', UpdateUserController::class)
        ->name('users.update');

    Route::middleware(['auth:sanctum', 'can:super-admin'])
        ->delete('/users/{user}', DeleteUserController::class)
        ->name('users.delete');

    Route::middleware(['auth:sanctum', 'can:super-admin'])
        ->post('/users/mass-delete', MassDeleteUserController::class)
        ->name('users.mass-delete');
});

Route::prefix('administrative')->name('administrative.')->group(function () {
    /**
     * DashBoard
     */
    Route::middleware(['auth:sanctum', 'can:administrative'])
        ->prefix('dashboard')
        ->name('dashboard')
        ->group(function () {
            Route::get('/', IndexDashboardController::class);
            Route::get('questionnaires', QuestionnaireController::class)->name('questionnaires');
            Route::get('question', QuestionController::class)->name('question');
        });

    /*
     * Authentication
     */
    Route::middleware(['guest', 'throttle:5,1'])
        ->post('/login', LogInController::class)
        ->name('login');
    Route::middleware(['auth:sanctum'])
        ->get('/user', ShowAuthUserController::class)
        ->name('user');
    Route::middleware(['auth:sanctum', 'can:administrative'])
        ->put('/profile/{user}', UpdateProfileController::class)
        ->name('profile');
    Route::middleware(['auth:sanctum'])
        ->post('/logout', LogOutController::class)
        ->name('logout');

    /*
     * Teams
     *
     */
    Route::name('teams.')->prefix('teams')->middleware(['auth:sanctum', 'can:administrative'])->group(function () {
        Route::get('/', IndexTeamController::class)->name('index');
        Route::get('{team}', ShowTeamController::class)->name('show');
        Route::post('/', StoreTeamController::class)->name('store');
        Route::put('{team}', UpdateTeamController::class)->name('update');
        Route::middleware(['can:super-admin'])->delete('{team}', DeleteTeamController::class)->name('delete');

        // Users
        Route::get('{team}/users', TeamUserIndexController::class)->name('users.index');
        Route::post('{team}/users/detach', DetachUserController::class)->name('users.detach');

        /**
         * Questionnaires
         */
        Route::get('{team}/questionnaires', IndexTeamQuestionnaireController::class)->name('questionnaires.index');
        Route::post('{team}/questionnaires/{questionnaireId}/attach', AttachTeamQuestionnaireController::class)->name('questionnaires.attach');
        Route::delete('{team}/questionnaires/{questionnaire}/detach', DetachTeamQuestionnaireController::class)->name('questionnaires.detach');

        // Users
        Route::get('questionnaires/{questionnaireTeam}/users', IndexTeamQuestionnaireUserController::class)->name('questionnaires.users.index');
    });

    /*
     * Users
     *
     */
    Route::name('users.')->prefix('users')->middleware(['auth:sanctum', 'can:administrative'])->group(function () {
        Route::get('/', IndexUserController::class)
            ->name('index');
        Route::get('/{user}', ShowUserController::class)
            ->name('show');

        // Questionnaires
        Route::get('{user}/questionnaires', UserQuestionnaireIndexController::class)
            ->name('questionnaires.index');
        Route::post('{user}/questionnaires/{questionnaireId}/attach', AttachQuestionnaireController::class)
            ->name('questionnaires.attach');
        Route::delete('{user}/questionnaires/{userQuestionnaireId}/detach', DetachQuestionnaireController::class)
            ->name('questionnaires.detach');
        Route::get('{user}/questionnaire/{userQuestionnaireId}/resend-notification',
            ResendQuestionnaireAttachedNotificationController::class)
            ->name('questionnaires.resendNotification');

        // Teams
        Route::get('{user}/teams', IndexUserTeamControllerAlias::class)->name('teams.index');
        Route::post('{user}/teams/attach', AttachTeamController::class)->name('teams.attach');
    });

    /*
     *Categories
     */
    Route::middleware(['auth:sanctum', 'can:administrative'])
        ->name('categories.')
        ->prefix('categories')
        ->group(function () {
            Route::get('/', IndexCategoryController::class)->name('index');
            Route::get('/{category}', ShowCategoryController::class)->name('show');
            Route::post('/', StoreCategoryController::class)->name('store');
            Route::put('/{category}', UpdateCategoryController::class)->name('update');
            Route::middleware(['can:super-admin'])->delete('/{category}',
                DeleteCategoryController::class)->name('delete');
        });

    /*
    *Questions
    */

    Route::middleware(['auth:sanctum', 'can:administrative'])
        ->name('questions.')
        ->prefix('questions')
        ->group(function () {
            Route::get('/', IndexQuestionController::class)->name('index');
            Route::get('/{question}', ShowQuestionController::class)->name('show');
            Route::middleware(['xss-protect'])->post('/', StoreQuestionController::class)->name('store');
            Route::middleware(['xss-protect'])->put('/{question}', UpdateQuestionController::class)->name('update');
            Route::delete('/{question}', DeleteQuestionController::class)->name('delete');
            Route::post('/mass-delete', MassDeleteQuestionController::class)->name('mass-delete');

            // Answers
            Route::name('answers.index')->get('{question}/answers',
                \App\Http\Controllers\Api\V1\Administrative\Question\Answer\IndexAnswerController::class);
            Route::name('answers.async')->post('{question}/answers', AsyncAnswerController::class);
        });

    /**
     * Answers
     */
    Route::middleware(['auth:sanctum', 'can:administrative'])
        ->name('answers.')
        ->prefix('answers')
        ->group(function () {
            Route::get('/{id}/exists', CheckAnswerExistsController::class)->name('checkExists');
            Route::get('/', IndexAnswerController::class)->name('index');
            Route::get('/{answer}', ShowAnswerController::class)->name('show');
            Route::middleware(['xss-protect'])->post('/', StoreAnswerController::class)->name('store');
            Route::middleware(['xss-protect'])->put('/{answer}', UpdateAnswerController::class)->name('update');
            Route::delete('/{answer}', DeleteAnswerController::class)->name('delete');
            Route::post('/mass-delete', MassDeleteAnswerController::class)->name('mass-delete');
        });

    /**
     * Questionnaire
     */
    Route::middleware(['auth:sanctum', 'can:administrative'])
        ->name('questionnaires.')
        ->prefix('questionnaires')
        ->group(function () {
            Route::get('/{questionnaireId}/check-available',
                \App\Http\Controllers\Api\V1\Administrative\Questionnaire\CheckQuestionnaireAvailableController::class)
                ->name('checkAvailable');
            Route::get('/', IndexQuestionnaireController::class)->name('index');
            Route::get('/{questionnaire}', ShowQuestionnaireController::class)->name('show');
            Route::middleware(['xss-protect'])->post('/', StoreQuestionnaireController::class)->name('store');
            Route::middleware(['xss-protect'])->put('/{questionnaire}',
                UpdateQuestionnaireController::class)->name('update');
            Route::delete('/{questionnaire}', DeleteQuestionnaireController::class)->name('delete');
            Route::post('/mass-delete', MassDeleteQuestionnaireController::class)->name('mass-delete');

            // Questions
            Route::get('/{questionnaire}/eligible/{questionId}',
                [EligibleQuestionController::class, 'find'])->name('questions.findEligibleQuestion');
            Route::get('/{questionnaire}/eligible-questions',
                [EligibleQuestionController::class, 'index'])->name('questions.eligibleQuestions');
            Route::name('questions.index')->get('{questionnaire}/questions',
                \App\Http\Controllers\Api\V1\Administrative\Questionnaire\Question\IndexQuestionController::class);
            Route::name('questions.sync')->post('{questionnaire}/questions',
                SyncQuestionController::class);
        });

    /**
     * Evaluations
     */
    Route::middleware(['auth:sanctum', 'can:administrative'])
        ->prefix('evaluations')
        ->name('evaluations.')
        ->group(function () {
            Route::get('/', IndexEvaluationController::class)->name('index');
            Route::get('{evaluation}', ShowEvaluationController::class)->name('show');
        });
});

Route::prefix('users')->name('users.')->group(function () {
    // Questionnaires
    Route::prefix('questionnaires')->name('questionnaires.')->group(function () {
        Route::middleware(['throttle:5,1'])
            ->get('{code}/check-available', CheckQuestionnaireAvailableController::class)
            ->name('checkAvailable');
        Route::middleware(['throttle:5,1'])
            ->get('{code}', \App\Http\Controllers\Api\V1\Regular\User\Questionnaire\ShowQuestionnaireController::class)
            ->name('show');
        Route::post('{code}/evaluate', EvaluateQuestionnaireController::class)
            ->name('evaluate');
    });
});

/*
 * File Uploads
 */

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('uploads/{type}/{modelId}', [FileUploadController::class, 'index'])
        ->name('uploads.index');
    Route::post('uploads/{type}/{id}', [FileUploadController::class, 'store'])
        ->name('uploads.store');
    Route::post('uploads-change-order/{type}', [FileUploadController::class, 'changeOrder'])
        ->name('uploads.changeOrder');
    Route::post('uploads-mass-delete/{type}', [FileUploadController::class, 'massDelete'])
        ->name('uploads.massDelete');
});

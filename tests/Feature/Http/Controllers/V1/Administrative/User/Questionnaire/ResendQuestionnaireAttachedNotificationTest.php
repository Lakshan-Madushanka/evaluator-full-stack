<?php

use App\Enums\Role;
use App\Models\User;
use App\Models\UserQuestionnaire;
use App\Notifications\QuestionnaireAttachedToUser;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $questionnaire = UserQuestionnaire::query()
        ->where('attempts', 0)
        ->first();
    $questionnaire->expires_at = now()->addMinutes(30);
    $questionnaire?->save();
    $questionnaire?->refresh();

    $this->questionnaire = $questionnaire;
});

it('return 401 response non-login users ', function () {
    $response = getJson(route('api.v1.administrative.users.questionnaires.resendNotification',
        [
            'user' => $this->questionnaire?->user_id,
            'userQuestionnaireId' => $this->questionnaire?->id,
        ]
    ));
    $response->assertUnauthorized();
})->group('administrative/users/questionnaires/resendNotification');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.questionnaires.resendNotification',
        [
            'user' => $this->questionnaire?->user_id,
            'userQuestionnaireId' => $this->questionnaire?->id,
        ]
    ));
    $response->assertNotFound();
})->group('administrative/users/questionnaires/resendNotification');

it('can resend notification to non attempted and not expired user questionnaire', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $user = User::findOrFail($this->questionnaire?->user_id);

    Notification::fake();

    $response = getJson(route('api.v1.administrative.users.questionnaires.resendNotification',
        [
            'user' => Hashids::encode($this->questionnaire?->user_id),
            'userQuestionnaireId' => Hashids::encode($this->questionnaire?->id),
        ]
    ));

    Notification::assertSentTo([$user], QuestionnaireAttachedToUser::class);
    $response->assertOk();
})->group('administrative/users/questionnaires/resendNotification');

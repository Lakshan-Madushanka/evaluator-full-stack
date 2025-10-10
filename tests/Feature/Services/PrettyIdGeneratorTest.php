<?php

use App\Models\Answer;
use App\Services\PrettyIdGenerator;
use Tests\Repositories\AnswerRepository;

use function PHPUnit\Framework\assertNotEmpty;

it('can get last inserted id of a model', function () {
    $id = PrettyIdGenerator::getLastInsertedId('answers');

    $lastInsertedId = Answer::orderByDesc('id')->first()->id;

    expect($id)->toBe($lastInsertedId);
})->group('pretty-id-generator');

it('can get pretty ids of a model', function () {
    $data = PrettyIdGenerator::getPrettyIds('answers');

    expect($data->count() > 0)->toBeTrue();
})->group('pretty-id-generator');

it('can check if pretty id exists', function () {
    $answer = AnswerRepository::getRandomAnswer();

    $initMethod = new ReflectionMethod(PrettyIdGenerator::class, 'init');
    $checkPrettyIdExistsMethod = new ReflectionMethod(PrettyIdGenerator::class, 'checkPrettyIdExists');

    $initMethod->setAccessible(true);
    $checkPrettyIdExistsMethod->setAccessible(true);

    $idGenerator = new PrettyIdGenerator;

    $initMethod->invoke($idGenerator, 'answers');

    $exists = $checkPrettyIdExistsMethod->invoke($idGenerator, $answer->pretty_id, 'answers');

    expect($exists)->toBeTrue();
})->group('pretty-id-generator');

it('can generate pretty id for a model', function () {
    $id = PrettyIdGenerator::generate('answers', 'ans_', 12);

    assertNotEmpty($id);
    expect(strlen($id))->toBe(12);
})->group('pretty-id-generatora');

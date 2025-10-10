<?php

namespace App\Events;

use App\Services\PrettyIdGenerator;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SetModelPrettyId
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $data = $this->getPrettyIdData($model);
        $model->pretty_id = PrettyIdGenerator::generate($data['table'], $data['prefix'], $data['length']);
    }

    public function getPrettyIdData(Model $model): array
    {
        return match (class_basename($model)) {
            'Answer' => ['table' => 'answers', 'prefix' => 'ans_', 'length' => 12],
            'Question' => ['table' => 'questions', 'prefix' => 'quest_', 'length' => 13]
        };
    }
}

<?php

namespace App\Http\Sorts\UserQuestionnaire;

use App\Models\Evaluation;
use App\Models\UserQuestionnaire;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class MarkSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->orderBy(
            Evaluation::select('marks_percentage')
                ->whereColumn((new UserQuestionnaire)->qualifyColumn('id'), (new Evaluation)->qualifyColumn('user_questionnaire_id'))
                ->limit(1),
            $direction
        );
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Administrative\Dashboard;

use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;

class IndexDashboardController
{
    public function __invoke(DashboardService $ds)
    {
        return new DashboardResource([
            'users_count' => $ds->getUsersCount(),
            'no_of_categories' => $ds->getTotalCategories(),
            'no_of_questionnaires' => $ds->getTotalQuestionnaires(),
            'no_of_questions' => $ds->getTotalQuestions(),
            'category_questionnaires' => $ds->getCategoriesQuestionnaires(),
            'category_questions' => $ds->getCategoriesQuestions(),
            'evaluations' => $ds->getLatestEvaluations(),
        ]);
    }
}

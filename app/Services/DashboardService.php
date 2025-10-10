<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\User;
use DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * @return object{super_admins: int, admins: int, regular_users: int, total_users: int}
     */
    public function getUsersCount(): object
    {
        $superAdminRole = Role::SUPER_ADMIN->value;
        $adminRole = Role::ADMIN->value;
        $regularUserRole = Role::REGULAR->value;

        $users = User::query()
            ->selectRaw("count(case when role = {$superAdminRole} then 1 end) super_admins")
            ->selectRaw("count(case when role = {$adminRole} then 1 end) admins")
            ->selectRaw("count(case when role = {$regularUserRole} then 1 end) regular_users")
            ->toBase()
            ->first();

        $users->total_users = collect($users)->sum();

        return $users;
    }

    public function getTotalCategories(): int
    {
        return Category::query()->count();
    }

    public function getTotalQuestionnaires(): int
    {
        return Questionnaire::query()->count();
    }

    public function getTotalQuestions(): int
    {
        return Question::query()->count();
    }

    /** @return Collection<array{name: string, count: int}> */
    public function getCategoriesQuestionnaires(): Collection
    {
        return $this->getCategorizableData('questionnaires', 1);

    }

    /** @return Collection<array{name: string, count: int}> */
    public function getCategoriesQuestions(): Collection
    {
        return $this->getCategorizableData('questions', 2);

    }

    /* *@return Collection<Evaluation> */
    public function getLatestEvaluations(): Collection
    {
        return Evaluation::limit(10)->with(['user', 'questionnaire'])->get();
    }

    /**
     * @return Collection<array{name: string, count: int}>
     */
    public function getCategorizableData(string $relatedTable, int $categorizableType): Collection
    {
        return DB::table('categories')
            ->selectRaw('categories.name as name, count(*) as count')
            ->join('categorizables', function (JoinClause $join) use ($categorizableType) {
                $join->on('categorizables.category_id', '=', 'categories.id')
                    // enforced morph map type see in AppServiceProvider
                    ->where('categorizable_type', $categorizableType);
            })
            ->join($relatedTable, "{$relatedTable}.id", '=', 'categorizables.categorizable_id')
            ->groupBy('categories.name')
            ->get()
            ->mapWithKeys(function ($data) {
                return [$data->name => $data->count];
            });
    }
}

<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\CategoryPosition;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\CategoryPositionRepositoryInterface;

class EloquentCategoryPositionRepository implements CategoryPositionRepositoryInterface
{
    public function findByDate(Carbon $date): array
    {
        return CategoryPosition::whereDate('date', $date->toDateString())
            ->get()
            ->pluck('position', 'category_id')
            ->mapWithKeys(fn($v,$k)=>[(string)$k => (int)$v])
            ->toArray();
    }

    public function upsertMany(string $date, array $data): void
    {
        $now = now();
        $rows = [];
        foreach ($data as $categoryId => $position) {
            $rows[] = [
                'date' => $date,
                'category_id' => $categoryId,
                'position' => $position,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        CategoryPosition::upsert($rows, ['date', 'category_id'], ['position', 'updated_at']);
    }
}

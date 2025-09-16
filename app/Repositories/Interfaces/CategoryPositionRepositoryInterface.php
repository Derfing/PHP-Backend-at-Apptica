<?php

namespace App\Repositories\Interfaces;

use Carbon\Carbon;
use App\Models\CategoryPosition;

interface CategoryPositionRepositoryInterface
{
    public function findByDate(Carbon $date): array;
    public function upsertMany(string $date, array $data): void;
}

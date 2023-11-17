<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;

class RangeService
{
    public function filterByRange(?string $range = '3m', Builder $query): Builder
    {
        return $query->where('committed_at', '>', now()->subMonths(3));
    }
}

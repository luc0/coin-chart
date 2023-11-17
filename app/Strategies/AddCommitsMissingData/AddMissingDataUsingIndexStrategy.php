<?php

namespace App\Strategies\AddCommitsMissingData;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class AddMissingDataUsingIndexStrategy implements AddMissingDataStrategy
{
    public function getFirstDateFrom(Collection $dates): string
    {
        return $dates->keys()->sortDesc()->first();
    }

    public function getLastDateFrom(Collection $dates): string
    {
        return $dates->keys()->sortDesc()->last();
    }

    public function hasNoDataInDate(Collection $dates, Carbon $searchDate): bool
    {
        return $dates->has($searchDate->toDateString()) == false;
    }

    public function addDate(Collection $dates, Carbon $addDate): Collection
    {
        $dates->put($addDate->toDateString(), 0);
        return $dates;
    }

    public function getArraySortedDesc(Collection $dates): array
    {
        return $dates->sortKeysDesc()->values()->toArray();
    }
}

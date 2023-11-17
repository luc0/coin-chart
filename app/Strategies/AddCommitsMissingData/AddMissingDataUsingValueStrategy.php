<?php

namespace App\Strategies\AddCommitsMissingData;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class AddMissingDataUsingValueStrategy implements AddMissingDataStrategy
{
    public function getFirstDateFrom(Collection $dates): string
    {
        return $dates->first();
    }

    public function getLastDateFrom(Collection $dates): string
    {
        return $dates->last();
    }

    public function hasNoDataInDate(Collection $dates, Carbon $searchDate): bool
    {
        return $dates->search($searchDate->toDateString()) == false;
    }

    public function addDate(Collection $dates, Carbon $addDate): Collection
    {
        $dates->add($addDate->toDateString());
        return $dates;
    }

    public function getArraySortedDesc(Collection $dates): array
    {
        return $dates->sortDesc()->values()->toArray();
    }
}

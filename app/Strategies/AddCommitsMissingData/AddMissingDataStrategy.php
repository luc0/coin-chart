<?php

namespace App\Strategies\AddCommitsMissingData;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface AddMissingDataStrategy
{
    public function getFirstDateFrom(Collection $dates): string;

    public function getLastDateFrom(Collection $dates): string;

    public function hasNoDataInDate(Collection $dates, Carbon $searchDate): bool;

    public function addDate(Collection $dates, Carbon $addDate): Collection;

    public function getArraySortedDesc(Collection $dates): array;
}

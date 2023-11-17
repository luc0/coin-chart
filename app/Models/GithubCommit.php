<?php

namespace App\Models;

use App\Enums\RangeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GithubCommit extends Model
{
    use HasFactory;

    protected $fillable = [
        'committed_at'
    ];


    public function scopeFilterByRange(Builder $query, RangeEnum $range): Builder
    {
        if ($range->isHoursUnit()) {
            return $query->where('committed_at', '>', now()->subHours($range->getValue()));
        }

        if ($range->isDaysUnit()) {
            return $query->where('committed_at', '>', now()->subDays($range->getValue()));
        }

        if ($range->isMonthUnit()) {
            return $query->where('committed_at', '>', now()->subMonths($range->getValue()));
        }

        if ($range->isYearUnit()) {
            return $query->where('committed_at', '>', now()->subYears($range->getValue()));
        }

        return $query;
    }
}

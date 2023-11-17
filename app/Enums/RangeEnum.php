<?php

namespace App\Enums;

class RangeEnum extends Enum
{
    const CASES = [
        self::LAST_3_HOURS,
        self::LAST_24_HOURS ,
        self::LAST_7_DAYS,
        self::LAST_30_DAYS ,
        self::LAST_3_MONTHS,
        self::LAST_1_YEAR,
        self::LAST_3_YEAR,
        self::LAST_5_YEAR,
    ];

    CONST LAST_3_HOURS = '3h';
    CONST LAST_24_HOURS = '24h';
    CONST LAST_7_DAYS = '7d';
    CONST LAST_30_DAYS = '30d';
    CONST LAST_3_MONTHS = '3m';
    CONST LAST_1_YEAR = '1y';
    CONST LAST_3_YEAR = '3y';
    CONST LAST_5_YEAR = '5y';

    public function isHoursUnit(): bool
    {
        return in_array($this->value, [self::LAST_3_HOURS, self::LAST_24_HOURS]);
    }

    public function isDaysUnit(): bool
    {
        return in_array($this->value, [self::LAST_7_DAYS, self::LAST_30_DAYS]);
    }

    public function isMonthUnit(): bool
    {
        return in_array($this->value, [self::LAST_3_MONTHS]);
    }

    public function isYearUnit(): bool
    {
        return in_array($this->value, [self::LAST_1_YEAR, self::LAST_3_YEAR, self::LAST_5_YEAR]);
    }

    public function getValue(): int
    {
        return (int)intval($this->value);
    }
}

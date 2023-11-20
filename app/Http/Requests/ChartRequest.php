<?php

namespace App\Http\Requests;

use App\Enums\RangeEnum;
use Illuminate\Foundation\Http\FormRequest;

class ChartRequest extends FormRequest
{

    const RANGE = 'range';
    const COINS = 'coins';
    const GROUPED = 'grouped';

    public function range(): RangeEnum
    {
        return RangeEnum::from(request()->get(self::RANGE) ?? RangeEnum::LAST_3_MONTHS);
    }

    public function coins(): array
    {
        return request()->get(self::COINS) ?? [];
    }

    public function grouped(): bool
    {
        return request()->get(self::GROUPED) ?? true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}

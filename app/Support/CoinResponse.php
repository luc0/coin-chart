<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CoinResponse
{
    private const RESPONSE_DATA = 'data.history';

    private $response;
    private $prices;
    private $dates;

    function __construct(Response $response)
    {
        $this->response = $response;
        
        $this->prices = collect(Arr::get($response, self::RESPONSE_DATA))->pluck('price'); // TODO: Could extend collection with dotPluck
        $this->dates = collect(Arr::get($response, self::RESPONSE_DATA))->pluck('timestamp');
    }

    public function failed() 
    {
        $this->response->failed();
    }

    public function successful()
    {
        $this->response->successful();
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function getFirstPrice(): int
    {
        return $this->prices->first();
    }

    public function getPriceChanges(): array
    {
        $basePrice = $this->getFirstPrice();

        return $this->getPrices()->map(function($price) use ($basePrice){
            $priceChange = ($price * 100 / $basePrice) - 100;
            return ceil($priceChange * 100) / 100;
        })->values()->toArray();
    }

    public function getDates(): array
    {
        /*
            - division meses:
                - si el dia es 1, devolver nombre del mes. SINO ''
        */
        // $dates = $this->dates->map(function($timestamp) {
        //     $date = Carbon::createFromTimestamp($timestamp);
        //     if($date->month == 1 && $date->day == 1) {
        //         return Carbon::createFromTimestamp($timestamp)->format('Y');
        //     }

        //     if($date->day == 1) {
        //         return Carbon::createFromTimestamp($timestamp)->format('F');
        //     }

        //     return Carbon::createFromTimestamp($timestamp)->format('d/m/Y H:i');
        // })->values()->toArray();

        // return $dates;
// dd($dates);
        // return $this->dates->map(function($timestamp) {
        //     return Carbon::createFromTimestamp($timestamp)->format('d/m/Y H:i');
        // })->values()->toArray();

        // dd($this->dates->map(function($timestamp) {
        //     return Carbon::createFromTimestamp($timestamp)->format('d/m/Y H:i');
        // })->values()->toArray());
        return $this->dates->toArray();
        // return $this->dates->map(function($timestamp) {
        //     return Carbon::createFromTimestamp($timestamp)->format('d/m/Y H:i');
        // })->values()->toArray();
    }
}

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

    public function getFirstPrice(): float
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
        return $this->dates->toArray();
    }
}

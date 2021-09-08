<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CoinRanking
{
    private const COIN_RANKING_API_URL = 'https://api.coinranking.com/v2/coin/Qwsogvtv82FCd/history';

    public function getCoinPriceHistory(?string $period = '1y'): Response 
    {
        return Http::get(self::COIN_RANKING_API_URL, ['timePeriod' => $period]);
    }

    public function getPrices(Response $data): array
    {
        return collect($data['data']['history'])
            ->pluck('price')
            ->values()->toArray();
    }

    public function getDate(Response $data): array
    {
        return collect($data['data']['history'])
            ->pluck('timestamp')
            ->map(function($timestamp) {
                return Carbon::createFromTimestamp($timestamp)->format('d/m/Y H:i');
            })
            ->values()->toArray();
    }
}

<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CoinRanking
{
    private const COIN_RANKING_API_URL = 'https://api.coinranking.com/v2/';
    private $coinIds = [];

    function __construct()
    {
        $this->coinIds = $this->getCoinsId();
    }

    public function getCoins(): Response
    {
        return Http::get($this->getCoinsEndpoint(), [
            'x-access-token' => 'coinranking5e43443bd45a48ac38744d5d22a78867b3f2d8fffc73660f'
        ]);
    }

    public function getCoinPriceHistory(?string $period = '1y', ?string $coin = 'BTC'): Response 
    {
        return Http::get(
            $this->getCoinHistoryEndpoint($this->getCoinUuid($coin)), 
            [
                'timePeriod' => $period,
                'x-access-token' => 'coinranking5e43443bd45a48ac38744d5d22a78867b3f2d8fffc73660f'
            ]
        );
    }

    public function listCoins(): array
    {
        $coins = $this->getCoins();

        return $coins['data']['coins'];
    }

    public function getCoinsId(): array
    {
        $coins = $this->getCoins();

        return collect($coins['data']['coins'])->mapWithKeys(function($item, $key) {
            return [$item['symbol'] => $item['uuid']];
        })->toArray();
    }

    public function getPrices(Response $data): array
    {
        return collect($data['data']['history'])
            ->pluck('price')
            ->values()->toArray();
    }

    public function getPriceChanges(Response $data): array
    {
        $prices = collect($data['data']['history'])
            ->pluck('price');
        $basePrice = $prices->first();

        $changes = $prices->map(function($price) use ($basePrice){
            $priceChange = ($price * 100 / $basePrice) - 100;
            return ceil($priceChange * 100) / 100;
        })->values()->toArray();

        return $changes;
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

    private function getCoinHistoryEndpoint($uuid): string
    {
        return self::COIN_RANKING_API_URL . 'coin/' . $uuid . '/history';
    }

    private function getCoinsEndpoint(): string
    {
        return self::COIN_RANKING_API_URL . 'coins';
    }

    private function getCoinUuid($coin): string
    {
        return $this->coinIds[$coin];
    }
}

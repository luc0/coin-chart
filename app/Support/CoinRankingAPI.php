<?php

namespace App\Support;

use App\Response\CoinGroupResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CoinRankingAPI
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

    public function getCoinsPriceHistory(?string $period = '3m', array $coins): CoinGroupResponse
    {
        $history = [];

        foreach($coins as $coin) {
            $response =  Http::get(
                $this->getCoinHistoryEndpoint($coin['uuid']),
                [
                    'timePeriod' => $period,
                    'x-access-token' => 'coinranking5e43443bd45a48ac38744d5d22a78867b3f2d8fffc73660f'
                ]
            );

            $history[$coin['symbol']] = new CoinResponse($response);
        }

        return new CoinGroupResponse($history);
    }

    public function listCoins(): array
    {
        $coins = $this->getCoins();

        return $coins['data']['coins'];
    }

    // public function listCoinsSymbol(): array
    // {
    //     return collect($this->listCoins())->map(function($item, $key) {
    //         return [
    //             'symbol' => $item['symbol'],
    //             'iconUrl' => $item['iconUrl']
    //         ];
    //     })->toArray();
    // }

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

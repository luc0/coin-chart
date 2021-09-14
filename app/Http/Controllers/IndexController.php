<?php

namespace App\Http\Controllers;

use App\Support\CoinRanking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index()
    {
        $coinRanking = new CoinRanking();
        
        $range = request()->get('range');
        $coin = request()->get('coin', 'BTC');

        $response = $coinRanking->getCoinPriceHistory($range, $coin);

        if($response->successful()) {
            $data = [
                'prices' => $coinRanking->getPrices($response),
                'time' => $coinRanking->getDate($response),
                'currentCoin' => $coin,
                'currentRange' => $range
            ];
        }

        if($response->failed()) {
            $data = [
                'error' => $response->json('message'),
                'currentCoin' => $coin,
                'currentRange' => $range
            ];
        }

        return Inertia::render('Welcome', $data);
    }
}

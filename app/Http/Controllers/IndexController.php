<?php

namespace App\Http\Controllers;

use App\Support\CoinRanking;
use Inertia\Inertia;

class IndexController extends Controller
{
    private const RANGES = ['3h', '24h', '7d', '30d', '3m', '1y', '3y', '5y'];

    public function index()
    {
        $coinRanking = new CoinRanking();
        
        $range = request()->get('range');
        $coins = request()->get('coins') ?? [];

        $responses = $coinRanking->getCoinsPriceHistory($range, $coins);
        $success = $responses->allSuccessful();
// dd($responses->getDates())
        $data = [
            'coinsList' => $coinRanking->listCoins($coins), // coinList
            'coinsSelected' => $coins, // currentCoins
            'filterRange' => $range, // currentRange
            'filterRangeList' => self::RANGES, // rangeList
            'chartPrices' => $success ? $responses->getPriceChanges() : null, // prices
            'chartDates' => $success ? $responses->getDates() : null, // time
            'error' => $success ? null : $responses->errorMessage()
        ];

        return Inertia::render('Welcome', $data);
    }
}

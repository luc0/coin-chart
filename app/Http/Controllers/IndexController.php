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
        $grouped = request()->get('grouped') ?? true;

        $responses = $coinRanking->getCoinsPriceHistory($range, $coins);
        $success = $responses->allSuccessful();

        // $chartValues = $grouped ? $responses->getPriceChanges($grouped) : null;
// dd($responses->getPriceChanges($grouped));
        $data = [
            'coinsList' => $coinRanking->listCoins($coins), // coinList
            'coinsSelected' => $coins, // currentCoins
            'grouped' => true,
            'filterRange' => $range, // currentRange
            'filterRangeList' => self::RANGES, // rangeList
            'chartPrices' => $success ? $responses->getPriceChanges($grouped) : null, // prices
            'chartDates' => $success ? $responses->getDates() : null, // time
            'error' => $success ? null : $responses->errorMessage()
        ];

        return Inertia::render('Welcome', $data);
    }
}

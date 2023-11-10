<?php

namespace App\Http\Controllers;

use App\Support\CoinRankingAPI;
use Inertia\Inertia;

class ListCoinsController extends Controller
{
    public function index()
    {
        $coinRanking = new CoinRankingAPI();

        return Inertia::render('ListCoins', [
            'coins' => $coinRanking->listCoins()
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Support\CoinRanking;
use Inertia\Inertia;

class ListCoinsController extends Controller
{
    public function index()
    {
        $coinRanking = new CoinRanking();

        return Inertia::render('ListCoins', [
            'coins' => $coinRanking->listCoins()
        ]);
    }
}

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
        $response = $coinRanking->getCoinPriceHistory($range);

        if($response->successful()) {
            $data = [
                'prices' => $coinRanking->getPrices($response),
                'time' => $coinRanking->getDate($response),
                'currentRange' => $range
            ];
        }

        if($response->failed()) {
            $data = [
                'error' => $response->json('message'),
                'currentRange' => $range
            ];
        }

        return Inertia::render('Welcome', $data);
    }
}

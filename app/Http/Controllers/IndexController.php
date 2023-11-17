<?php

namespace App\Http\Controllers;

use App\Enums\RangeEnum;
use App\Services\CryptoService;
use App\Support\CoinRankingAPI;
use Inertia\Inertia;

class IndexController extends Controller
{
    private const RANGES = ['3h', '24h', '7d', '30d', '3m', '1y', '3y', '5y'];

    public function index(CryptoService $cryptoService)
    {
        $coinRankingAPI = new CoinRankingAPI();

        // TODO: create Request
        $range = RangeEnum::from(request()->get('range') ?? '3m');
        $coins = request()->get('coins') ?? [];
        $grouped = request()->get('grouped') ?? true;

        $coinRankingPriceResponse = $coinRankingAPI->getCoinsPriceHistory($range, $coins);
        $coinRankingSuccess = $coinRankingPriceResponse->allSuccessful();

        // $chartValues = $grouped ? $coinRankingPriceResponse->getPriceChanges($grouped) : null;
        // dd($coinRankingPriceResponse->getPriceChanges($grouped));
//        dd($coinRanking->listCoins($coins))

        $githubCommitsDates = $cryptoService->getCommitsDates($coins, $range);
        $githubCommitsCount = $cryptoService->getCommitsCount($coins, $range);

        $data = [
            'coinsList' => $coinRankingAPI->listCoins($coins), // coinList
            'coinsSelected' => $coins, // currentCoins
            'grouped' => true,

            'filterRange' => $range, // currentRange
            'filterRangeList' => self::RANGES, // rangeList

            'chartPrices' => $coinRankingSuccess ? $coinRankingPriceResponse->getPriceChanges($grouped) : null, // prices
            'chartDates' => $coinRankingSuccess ? $coinRankingPriceResponse->getDates() : null, // time
            'error' => $coinRankingSuccess ? null : $coinRankingPriceResponse->errorMessage(),

            'chartGithubCommits' => $githubCommitsCount,
            'chartGithubCommitsDates' => $githubCommitsDates,
        ];

        return Inertia::render('Welcome', $data);
    }
}

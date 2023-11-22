<?php

namespace App\Http\Controllers;

use App\Enums\RangeEnum;
use App\Http\Requests\ChartRequest;
use App\Services\CryptoService;
use App\Support\CoinRankingAPI;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index(CryptoService $cryptoService, ChartRequest $chartRequest)
    {
        $coinRankingAPI = new CoinRankingAPI();

        $range = $chartRequest->range();
        $coins = $chartRequest->coins();
        $grouped = $chartRequest->grouped();

        $coinRankingPriceResponse = $coinRankingAPI->getCoinsPriceHistory($range, $coins);
        $coinRankingSuccess = $coinRankingPriceResponse->allSuccessful();

        // $chartValues = $grouped ? $coinRankingPriceResponse->getPriceChanges($grouped) : null;

        $githubCommitsDates = $cryptoService->getCommitsDates($coins, $range);
        $githubCommitsCount = $cryptoService->getCommitsCount($coins, $range);

        $data = [
            'coinsList' => $coinRankingAPI->listCoins($coins), // coinList
            'coinsSelected' => $coins, // currentCoins
            'grouped' => true,

            'filterRange' => $range, // currentRange
            'filterRangeList' => RangeEnum::CASES, // rangeList

            'chartPrices' => $coinRankingSuccess ? $coinRankingPriceResponse->getPriceChanges($grouped) : null, // prices
            'chartDates' => $coinRankingSuccess ? $coinRankingPriceResponse->getDates() : null, // time
            'error' => $coinRankingSuccess ? null : $coinRankingPriceResponse->errorMessage(),

            'chartGithubCommits' => $githubCommitsCount,
            'chartGithubCommitsDates' => $githubCommitsDates,
        ];

        return Inertia::render('Charts', $data);
    }
}

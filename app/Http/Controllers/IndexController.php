<?php

namespace App\Http\Controllers;

use App\Services\Cryptos;
use App\Support\CoinRankingAPI;
use App\Support\GithubAPI;
use Inertia\Inertia;

class IndexController extends Controller
{
    private const RANGES = ['3h', '24h', '7d', '30d', '3m', '1y', '3y', '5y'];

    public function index(Cryptos $cryptoService)
    {
        $coinRankingAPI = new CoinRankingAPI();
        $githubAPI = new GithubAPI();

        $range = request()->get('range');
        $coins = request()->get('coins') ?? [];
        $grouped = request()->get('grouped') ?? true;

        $coinRankingPriceResponse = $coinRankingAPI->getCoinsPriceHistory($range, $coins);
        $coinRankingSuccess = $coinRankingPriceResponse->allSuccessful();

        // $chartValues = $grouped ? $coinRankingPriceResponse->getPriceChanges($grouped) : null;
        // dd($coinRankingPriceResponse->getPriceChanges($grouped));
//        dd($coinRanking->listCoins($coins))

//        $cryptoService->syncGithubData();
        $githubCommitsDates = $cryptoService->getCommitsDates();
        $githubCommitsCount = $cryptoService->getCommitsCount();
//        dd($githubCommitsDates, $githubCommitsCount);

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

        /*
         * - TODO: usar chartCommits en un chart.
         * */

        return Inertia::render('Welcome', $data);
    }
}

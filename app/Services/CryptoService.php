<?php

namespace App\Services;

use App\Enums\RangeEnum;
use App\Models\Crypto;
use App\Models\GithubCommit;
use App\Models\GithubProject;
use App\Strategies\AddCommitsMissingData\AddMissingDataStrategy;
use App\Strategies\AddCommitsMissingData\AddMissingDataUsingIndexStrategy;
use App\Strategies\AddCommitsMissingData\AddMissingDataUsingValueStrategy;
use App\Support\GithubAPI;
use Carbon\Carbon;

class CryptoService
{
    private GithubAPI $githubAPI;
    private ProjectService $projectService;

    function __construct(GithubAPI $githubAPI, ProjectService $projectService)
    {
        $this->githubAPI = $githubAPI;
        $this->projectService = $projectService;
    }

    public function syncGithubData(): bool
    {
        Crypto::all()->each(function ($crypto) {
            $project = $crypto->githubProject;

            $commits = $this->githubAPI->getAPICommits($project);

            $this->projectService->storeCommits($project, $commits);
        });

        return true;
    }

    // TODO: Aplicar single responsability
    public function getCommitsDates(?array $coins = [], RangeEnum $range): array
    {
        $tokens = $coins ? collect($coins)->pluck('symbol') : [];

        $cryptoWithCommits = Crypto::with(['githubProject.githubCommits' => function($query) use($range) {
            return $query->filterByRange($range);
        }])
            ->whereIn('token', $tokens)
            ->get();

        $dates = $cryptoWithCommits->map(function($crypto) {
            return $crypto->githubProject->githubCommits->map(function ($commit) {
                return $commit->committed_at = explode(' ', $commit->committed_at)[0];
            })->unique()->values()->toArray();
        })->unique()->values()->toArray();

        if (!$dates) {
            return [];
        }

        $dates = $this->addMissingDates($dates[0], new AddMissingDataUsingValueStrategy());

        return [$dates];
    }

    // TODO: Aplicar single responsability
    public function getCommitsCount(?array $coins = [], RangeEnum $range): array
    {
        $tokens = $coins ? collect($coins)->pluck('symbol') : [];

        $cryptosWithCommits = Crypto::with(['githubProject.githubCommits' => function($query) use($range) {
            return $query->filterByRange($range);
        }])->whereIn('token', $tokens)->get();

        $cryptosWithCommits = $cryptosWithCommits->mapWithKeys(function($crypto, $key) {
            $commitsPerDate = $crypto->githubProject->githubCommits->countBy(function (GithubCommit $commit) {
                return explode(' ', $commit->committed_at)[0];
            })->toArray();
            return [$crypto->token => $commitsPerDate];
        })->unique()->toArray();

        if (!$cryptosWithCommits) {
            return [];
        }

        return collect($cryptosWithCommits)->map(function (array $crypto) {
            return $this->addMissingDates($crypto, new AddMissingDataUsingIndexStrategy());
        })->toArray();
    }

    // TODO: naming of interfaces, and strategy.
    private function addMissingDates(array $fromDates, AddMissingDataStrategy $addMissingData): array
    {
        $dates = collect($fromDates);

        $recentDate = Carbon::parse($addMissingData->getFirstDateFrom($dates));
        $oldestDate = Carbon::parse($addMissingData->getLastDateFrom($dates));

        $searchDate = $recentDate;
        $currentDaysCount = $recentDate->diffInDays($oldestDate);
        for ($i = 0; $i < $currentDaysCount; $i++) {
            $searchDate = $searchDate->subDay();

            $noDataDate = $addMissingData->hasNoDataInDate($dates, $searchDate);

            if ($noDataDate) {
                $dates = $addMissingData->addDate($dates, $searchDate);
            }
        }

        return $addMissingData->getArraySortedDesc($dates);
    }
}

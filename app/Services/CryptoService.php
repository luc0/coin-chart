<?php

namespace App\Services;

use App\Enums\RangeEnum;
use App\Models\Crypto;
use App\Models\GithubCommit;
use App\Strategies\AddCommitsMissingData\AddMissingDataStrategy;
use App\Strategies\AddCommitsMissingData\AddMissingDataUsingIndexStrategy;
use App\Strategies\AddCommitsMissingData\AddMissingDataUsingValueStrategy;
use App\Support\GithubAPI;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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

    public function getCommitsDates(?array $cryptoData = [], RangeEnum $range): array
    {
        $token = $cryptoData ? collect($cryptoData)->pluck('symbol') : [];

        $cryptoWithCommits = $this->getCryptoWithCommits($token, $range);

        $dates = $this->listCommitedAtToDateString($cryptoWithCommits);

        if (!$dates) {
            return [];
        }

        // TODO: only add for the first crypto? $dates[0] (this needs a fix for multiple crypto)
        $dates = $this->addMissingDatesTo($dates[0], new AddMissingDataUsingValueStrategy());

        return [$dates];
    }

    public function getCommitsCount(?array $cryptoDate = [], RangeEnum $range): array
    {
        $crypto = $cryptoDate ? collect($cryptoDate)->pluck('symbol') : [];

        $cryptosWithCommits = $this->getCryptoWithCommits($crypto, $range);

        $cryptosWithCommitDates = $this->listCommitedAtToDateStringAsIndex($cryptosWithCommits);

        if (!$cryptosWithCommitDates) {
            return [];
        }

        return collect($cryptosWithCommitDates)->each(fn (array $dates) => (
            $this->addMissingDatesTo($dates, new AddMissingDataUsingIndexStrategy())
        ))->toArray();
    }

    private function addMissingDatesTo(array $commitDates, AddMissingDataStrategy $addMissingData): array
    {
        if (!$commitDates) {
            return $commitDates;
        }

        $dates = collect($commitDates);

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

    /**
     * @param array|Collection $tokens
     * @param RangeEnum $range
     * @return Builder[]|Collection
     */
    private function getCryptoWithCommits(array|Collection $tokens, RangeEnum $range): array|Collection
    {
        return Crypto::with(['githubProject.githubCommits' => fn ($query) => ($query->filterByRange($range))])
            ->whereIn('token', $tokens)
            ->get();
    }

    /**
     * @param array|Collection $cryptoWithCommits
     * @return array
     */
    private function listCommitedAtToDateString(array|Collection $cryptoWithCommits): array
    {
        return $cryptoWithCommits->map(fn($crypto) => (
            $crypto->githubProject->githubCommits->map(fn($commit) => (
                $commit->committed_at = explode(' ', $commit->committed_at)[0]
            ))->unique()->values()->toArray()
        ))->unique()->values()->toArray();
    }

    private function listCommitedAtToDateStringAsIndex(array|Collection $cryptosWithCommits): array
    {
        return $cryptosWithCommits->mapWithKeys(function($crypto, $key) {
            $commitsPerDate = $crypto->githubProject->githubCommits->countBy(function (GithubCommit $commit) {
                return explode(' ', $commit->committed_at)[0];
            })->toArray();
            return [$crypto->token => $commitsPerDate];
        })->unique()->toArray();
    }
}

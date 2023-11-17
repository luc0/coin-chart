<?php

namespace App\Services;

use App\Enums\RangeEnum;
use App\Models\Crypto;
use App\Models\GithubCommit;
use App\Models\GithubProject;
use App\Support\GithubAPI;
use Carbon\Carbon;

class CryptoService
{
    private GithubAPI $githubAPI;

    function __construct(GithubAPI $githubAPI)
    {
        $this->githubAPI = $githubAPI;
    }

    public function syncGithubData(): bool
    {
        /** @var Crypto $crypto */
        $cryptos = Crypto::all();

        $cryptos->map(function ($crypto) {
            /** @var GithubProject $project */
            $project = $crypto->githubProject;

            $lastStoredCommit = $project->githubCommits()->orderByDesc('committed_at')->first();
            $lastCommitAt = $lastStoredCommit?->committed_at;

            $commitsDTO = $this->githubAPI->getAllCommitsData($project->owner_name, $project->repository_name, $lastCommitAt);

            collect($commitsDTO->commits)->each(fn (string $commitedAt) => (
                $project->githubCommits()->create(['committed_at' => Carbon::parse($commitedAt)->toDateTime()])
            ));

            $project->save();
        });

        return true;
    }

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

        $dates = $this->addMissingDates($dates[0], true);

        return [$dates];
    }

    public function addMissingDates(array $fromDates, bool $searchInValue): array
    {
        $dates = collect($fromDates);
        if ($searchInValue === true) {
            $recentDate = Carbon::parse($dates->first());
            $oldestDate = Carbon::parse($dates->last());
        } else {
            $recentDate = Carbon::parse($dates->keys()->sortDesc()->first());
            $oldestDate = Carbon::parse($dates->keys()->sortDesc()->last());
        }
        $searchDate = $recentDate;
        $currentDaysCount = $recentDate->diffInDays($oldestDate);
        for ($i = 0; $i < $currentDaysCount; $i++) {
            $searchDate = $searchDate->subDay();

            if ($searchInValue === true) {
                $noDataDate = $dates->search($searchDate->toDateString()) == false;
            } else {
                $noDataDate = $dates->has($searchDate->toDateString()) == false;
            }

            if ($noDataDate) {
                if ($searchInValue) {
                    $dates->add($searchDate->toDateString());
                } else {
                    $dates->put($searchDate->toDateString(), 0);
                }
            }
        }

        if ($searchInValue) {
            return $dates->sortDesc()->values()->toArray();
        } else {
            return $dates->sortKeysDesc()->values()->toArray();
        }
    }

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

        $cryptosWithCommits = collect($cryptosWithCommits)->map(function (array $crypto) {
            return $this->addMissingDates($crypto, false);
        })->toArray();

        return $cryptosWithCommits;
    }
}
<?php

namespace App\Services;

use App\Models\Crypto;
use App\Models\GithubCommit;
use App\Models\GithubProject;
use App\Support\GithubAPI;
use Carbon\Carbon;

class Cryptos
{
    private GithubAPI $githubAPI;

    function __construct(GithubAPI $githubAPI)
    {
        $this->githubAPI = $githubAPI;
    }

    public function syncGithubData(): bool
    {
        /** @var Crypto $crypto */
        $crypto = Crypto::firstWhere(['name' => 'solana']);
        /** @var GithubProject $project */
        $project = $crypto->githubProject;

        $lastStoredCommit = $project->githubCommits()->orderByDesc('committed_at')->first();
        $lastCommitAt = $lastStoredCommit?->committed_at;

        $commitsDTO = $this->githubAPI->getAllCommitsData($project->owner_name, $project->repository_name, $lastCommitAt);

        collect($commitsDTO->commits)->each(fn (string $commitedAt) => (
            $project->githubCommits()->create(['committed_at' => Carbon::parse($commitedAt)->toDateTime()])
        ));

        $project->save();

        return true;
    }

    public function getCommitsDates(): array
    {
        return GithubCommit::all()->map(function($commit) {
            return $commit->committed_at = explode(' ', $commit->committed_at)[0];
        })->unique()->values()->toArray();
    }

    public function getCommitsCount(): array
    {
        return [
            'average' => GithubCommit::all()->countBy(function(GithubCommit $commit) {
//                dd($commit->committed_at);
                return explode(' ', $commit->committed_at)[0];
            })->values()
        ];
    }
}

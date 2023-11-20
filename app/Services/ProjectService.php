<?php

namespace App\Services;

use App\Models\GithubProject;
use App\Support\GithubAPI;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProjectService
{
    private GithubAPI $githubAPI;

    function __construct(GithubAPI $githubAPI)
    {
        $this->githubAPI = $githubAPI;
    }

    public function storeCommits(GithubProject $project, Collection $allAPICommits): void
    {
        $allAPICommits->each(fn (string $commitedAt) => (
            $project->githubCommits()->create(['committed_at' => Carbon::parse($commitedAt)->toDateTime()])
        ));

        $project->save();
    }
}

<?php

namespace App\Support;

use App\Dto\GithubCommitsResponsesDTO;
use App\Models\GithubProject;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

// DOCS: https://docs.github.com/en/rest/commits/commits?apiVersion=2022-11-28#get-a-commit
class GithubAPI
{
    private const API_URL = 'https://api.github.com';
    private const API_VERSION_HEADER = '2022-11-28';
    private const ACCEPT_HEADER = 'application/vnd.github+json';
    private const PER_PAGE = 100; // This is the max permitted
//    private $coinIds = [];

    function __construct()
    {
//        $this->coinIds = $this->getCoinsId();
    }

    // TODO: recorrer todas las pages.
    public function getCommitsResponse(string $owner, string $repository, ?string $since = null, ?int $currentPage = 1): GithubCommitsResponse
    {
        $response = Http::withToken(config('github.token'))
            ->get($this->getCommitsEndpoint($owner, $repository), [
            'since' => $since ? Carbon::parse($since)->toIso8601ZuluString() : null,
            'X-GitHub-Api-Version' => self::API_VERSION_HEADER,
            'Accept' => self::ACCEPT_HEADER,
            'per_page' => self::PER_PAGE,
            'page' => $currentPage,
        ]);

        return new GithubCommitsResponse($response);
    }

    public function getAPICommits(GithubProject $project): Collection
    {
        return collect($this->getAllCommitsData(
            $project->owner_name,
            $project->repository_name,
            $project->getLatsCommitAt()
        )->commits);
    }

    private function getAllCommitsData(string $owner, string $repository, ?string $since = null, ?int $currentPage = 1): GithubCommitsResponsesDTO
    {
        $hasMorePages = true;

        $commitsDTO = new GithubCommitsResponsesDTO();

        $githubCommitsResponse = $this->getCommitsResponse($owner, $repository, $since, $currentPage);
        $commitsDTO->addResponse($githubCommitsResponse);

        do {
            if ($githubCommitsResponse->areRemainingDataInOtherPages(self::PER_PAGE)) {
                $githubCommitsResponse = $this->getCommitsResponse($owner, $repository, $since, ++$currentPage);
                $commitsDTO->addResponse($githubCommitsResponse);
            } else {
                $hasMorePages = false;
            }
        } while ($hasMorePages && $currentPage < 2);

        return $commitsDTO;
    }

    private function getCommitsEndpoint($owner, $repo): string
    {
        return self::API_URL . '/repos/' . $owner . '/'. $repo .'/commits';
    }

//    private function getCoinUuid($coin): string
//    {
//        return $this->coinIds[$coin];
//    }
}

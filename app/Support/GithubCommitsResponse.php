<?php

namespace App\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

// DOCS: https://docs.github.com/en/rest/commits/commits?apiVersion=2022-11-28#get-a-commit
class GithubCommitsResponse
{
    private const RESPONSE_DATA = ''; // TODO: No hay que meterse en la resp esto se puede borrar? ver como unificar con el de CoinRankingAPI

    private $response;
    private $commits;

    function __construct(Response $response)
    {
        $this->response = $response;

        $body = json_decode($response->body());
        $this->commits = collect($body)->map(function ($commit) {
            return $commit?->commit->committer->date ?? null;
        })->reverse(); // TODO: Could extend collection with dotPluck
    }

    public function failed()
    {
        $this->response->failed();
    }

    public function successful(): bool
    {
        return $this->response->successful();
    }

    /**
     * @param $maxPerPage
     * @return bool
     * If data returned = maxPerPage probably there's more data in other pages.
     */
    public function areRemainingDataInOtherPages($maxPerPage): bool
    {
        return $this->commits->count() == $maxPerPage;
    }

    public function getCount(): array
    {
        return [
            'average' => $this->commits->countBy(function($commitDate) {
                return explode('T', $commitDate)[0];
            })->values()
        ];
    }

    public function getDates(): Collection
    {
        return $this->commits;
    }
}

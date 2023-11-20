<?php

namespace App\Dto;

use App\Support\GithubCommitsResponse;

class GithubCommitsResponsesDTO
{
    public function __construct(
        public array $commits = []
    ) {
    }

    public function addResponse(GithubCommitsResponse $response)
    {
        $this->commits = array_merge($this->commits, $response->getDates()->reverse()->toArray());
    }
}

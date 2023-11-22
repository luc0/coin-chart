<?php

namespace App\Response;

use App\Support\CoinResponse;

class CoinGroupResponse
{
    private $responses;

    public function __construct(array $responses)
    {
        $this->responses = $responses;
    }

    public function allSuccessful(): bool
    {
        foreach($this->responses as $response) {
            if($response->failed()) {
                return false;
            }
        }

        return true;
    }

    public function errorMessage()
    {
        return $this->responses[0]->json('message'); // TODO: show all messages.
    }

    public function getPriceChanges(int $grouped): array
    {
        // TODO: should call a service, with this logic. Refactor to collection map.
        $allChanges = [];

        foreach($this->responses as $coin => $coinResponse) {
            /** @var CoinResponse $coinResponse */
            $allChanges[$coin] = $coinResponse->getPriceChanges();
        }

        if($grouped) {
            return [
                'average' => $this->averageChanges($allChanges)->toArray()
            ];
        }

        return $allChanges;
    }

    public function getDates(): array
    {
        if(!$this->responses) {
            return [];
        }

        /** @var CoinResponse $firstCoinResponse */
        $firstCoinResponse = collect($this->responses)->first();

        return $firstCoinResponse->getDates();
    }

    // TODO: service
    private function sumAllChanges($allChanges): array
    {
        $sumChanges = [];

        foreach($allChanges as $coinChanges) {
            foreach($coinChanges as $i => $change) {
                if(!isset($sumChanges[$i])) {
                    $sumChanges[$i] = $change;
                    continue;
                }
                $sumChanges[$i] += $change;
            }
        }

        return $sumChanges;
    }

    // TODO: service
    private function averageChanges($allChanges)
    {
        $sumChanges = $this->sumAllChanges($allChanges);

        return collect($sumChanges)->map(function($changes) {
            return $changes / count($this->responses);
        });
    }
}

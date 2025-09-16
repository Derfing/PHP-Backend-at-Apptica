<?php

namespace App\Services;

use App\Exceptions\AppticaApiException;
use Carbon\Carbon;
use App\Clients\AppticaApiClient;
use Illuminate\Http\Client\ConnectionException;
use App\Repositories\Interfaces\CategoryPositionRepositoryInterface;

class AppTopService
{
    public function __construct(
        protected CategoryPositionRepositoryInterface $repo,
        protected AppticaApiClient $apiClient
    ) {}

    /**
     * @throws ConnectionException
     * @throws AppticaApiException
     */
    public function getPositionsByDate(int $applicationId, int $countryId, Carbon $date): array
    {
        $fromDb = $this->repo->findByDate($date);
        if (!empty($fromDb)) {
            return $fromDb;
        }

        $apiData = $this->apiClient->getTopHistory($applicationId, $countryId, $date->toDateString(), $date->toDateString());
        if (!$apiData) {
            return [];
        }

        $aggregated = collect($apiData)
            ->mapWithKeys(function ($subcategories, $categoryId) use ($date) {
                $positions = collect($subcategories)
                    ->map(fn($dates) => $dates[$date->toDateString()] ?? null)
                    ->filter(fn($pos) => !is_null($pos))
                    ->map(fn($pos) => (int) $pos) ;

                return $positions->isEmpty() ? [] : [(string)$categoryId => $positions->min()];
            })
            ->toArray();

        $this->repo->upsertMany($date->toDateString(), $aggregated);

        return $aggregated;
    }
}

<?php

namespace App\Clients;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use App\Exceptions\AppticaApiException;

class AppticaApiClient
{
    private string $apiKey;
    private int $maxAttempts = 3;
    private int $timeout = 10;

    public function __construct()
    {
        $this->apiKey = config('apptica_api.secret');
    }

    /**
     * @throws AppticaApiException
     * @throws ConnectionException
     */
    private function get(string $url, array $params = []): array
    {
        $params['B4NKGg'] = $this->apiKey;

        for ($attempt = 1; $attempt <= $this->maxAttempts; $attempt++) {
            $response = Http::timeout($this->timeout)->get($url, $params);

            if ($response->successful()) {
                $json = $response->json();

                if (isset($json['data']) && is_array($json['data'])) {
                    return $json['data'];
                }

                throw new AppticaApiException(
                    'Apptica returned unexpected structure',
                    $json,
                    500
                );
            }

            sleep($attempt);
        }

        throw new AppticaApiException(
            'Apptica fetch failed after '.$this->maxAttempts.' attempts',
            [],
            $response?->status() ?? 500
        );
    }


    /**
     * @throws ConnectionException
     * @throws AppticaApiException
     */
    public function getTopHistory(int $applicationId, int $countryId, string $dateFrom, string $dateTo): array
    {
        $url = "https://api.apptica.com/package/top_history/{$applicationId}/{$countryId}";
        $params = [
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
        ];

        return $this->get($url, $params);
    }
}

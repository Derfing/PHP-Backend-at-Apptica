<?php

namespace Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Tests\TestCase;
use App\Clients\AppticaApiClient;
use Illuminate\Support\Facades\Http;
use App\Exceptions\AppticaApiException;

class AppticaApiClientTest extends TestCase
{
    private AppticaApiClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new AppticaApiClient();
    }

    /**
     * @throws AppticaApiException
     * @throws ConnectionException
     */
    public function test_successful_response_returns_data(): void
    {
        Http::fake([
            '*' => Http::response([
                'status' => 200,
                'data' => [
                    '2' => [
                        '1' => [
                            '2025-08-20' => 53
                        ]
                    ]
                ]
            ], 200)
        ]);

        $data = $this->client->getTopHistory(1421444, 1, '2025-08-20', '2025-08-20');

        $this->assertIsArray($data);
        $this->assertArrayHasKey('2', $data);
    }

    /**
     * @throws ConnectionException
     */
    public function test_invalid_structure_throws_exception(): void
    {
        Http::fake([
            '*' => Http::response([
                'status' => 200,
                'unexpected' => []
            ], 200)
        ]);

        $this->expectException(AppticaApiException::class);
        $this->client->getTopHistory(1421444, 1, '2025-08-20', '2025-08-20');
    }

    /**
     * @throws ConnectionException
     */
    public function test_http_failure_throws_exception(): void
    {
        Http::fake([
            '*' => Http::response([], 500)
        ]);

        $this->expectException(AppticaApiException::class);
        $this->client->getTopHistory(1421444, 1, '2025-08-20', '2025-08-20');
    }
}

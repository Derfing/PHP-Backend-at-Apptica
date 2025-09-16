<?php

namespace Tests\Feature;

use Random\RandomException;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class AppTopCategoryTest extends TestCase
{
    public function test_returns_aggregated_positions(): void
    {
        $this->artisan('migrate');
        $date = '2025-08-20';

        Http::fake([
            'api.apptica.com/*' => Http::response([
                'status_code' => 200,
                'message' => 'ok',
                'data' => [
                    '2' => [
                        '1' => ['2025-08-20' => 53, '2025-08-19' => 55],
                        '2' => ['2025-08-20' => 50],
                    ],
                    '23' => [
                        '1' => ['2025-08-20' => 10],
                        '3' => ['2025-08-20' => 20],
                    ],
                    '99' => [
                        '5' => ['2025-08-20' => null],
                    ],
                ]
            ], 200)
        ]);

        $response = $this->getJson("/api/appTopCategory?date={$date}");

        $response->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'message' => 'ok',
                'data' => [
                    '2' => 50,
                    '23' => 10,
                ]
            ]);
    }

    public function test_throws_exception_on_apptica_error(): void
    {
        $this->artisan('migrate');
        $date = '2025-08-20';

        Http::fake([
            'api.apptica.com/*' => Http::response([], 500)
        ]);

        $response = $this->getJson("/api/appTopCategory?date={$date}");

        $response->assertStatus(500)
            ->assertJson([
                'status_code' => 500,
                'message' => 'Apptica fetch failed after 3 attempts',
            ]);
    }

    public function test_ignores_null_positions(): void
    {
        $this->artisan('migrate');
        $date = '2025-08-20';

        Http::fake([
            'api.apptica.com/*' => Http::response([
                'status_code' => 200,
                'message' => 'ok',
                'data' => [
                    '5' => [
                        '1' => ['2025-08-20' => null],
                        '2' => ['2025-08-20' => 15],
                    ]
                ]
            ], 200)
        ]);

        $response = $this->getJson("/api/appTopCategory?date={$date}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    '5' => 15,
                ]
            ]);
    }

    /**
     * @throws RandomException
     */
    public function test_handles_many_categories(): void
    {
        $this->artisan('migrate');
        $date = '2025-08-20';
        $data = [];

        for ($cat = 1; $cat <= 50; $cat++) {
            for ($sub = 1; $sub <= 3; $sub++) {
                $data[$cat][$sub][$date] = random_int(1, 1000);
            }
        }

        Http::fake([
            'api.apptica.com/*' => Http::response([
                'status_code' => 200,
                'message' => 'ok',
                'data' => $data
            ], 200)
        ]);

        $response = $this->getJson("/api/appTopCategory?date={$date}");

        $response->assertStatus(200);

        $json = $response->json('data');

        foreach ($data as $catId => $subcats) {
            $positions = array_map(fn($sub) => $sub[$date], $subcats);
            $this->assertEquals(min($positions), $json[$catId]);
        }
    }
}

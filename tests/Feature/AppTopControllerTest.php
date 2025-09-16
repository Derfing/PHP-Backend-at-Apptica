<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\AppticaApiException;

class AppTopControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_exception_renders_json(): void
    {
        $exception = new AppticaApiException(
            'Apptica error',
            ['details' => 'something went wrong'],
            500
        );

        $response = $exception->render(request());

        $this->assertEquals(500, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertEquals([
            'status_code' => 500,
            'message' => 'Apptica error',
            'data' => ['details' => 'something went wrong']
        ], $data);
    }
}

<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppticaApiException extends Exception
{
    public array $responseBody = [];

    public function __construct(string $message, array $responseBody = [], int $code = 500)
    {
        parent::__construct($message, $code);
        $this->responseBody = $responseBody;
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'status_code' => $this->getCode() ?: 500,
            'message' => $this->getMessage(),
            'data' => $this->responseBody,
        ], $this->getCode() ?: 500);
    }
}

<?php

namespace App\Http\Controllers;

use App\Exceptions\AppticaApiException;
use App\Http\Requests\GetAppTopCategory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use App\Services\AppTopService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AppTopController extends Controller
{
    public function __construct(protected AppTopService $service) {}

    /**
     * @throws ConnectionException
     * @throws AppticaApiException
     */
    public function index(GetAppTopCategory $request): \Illuminate\Http\JsonResponse
    {
        $date = Carbon::createFromFormat('Y-m-d', $request->validated('date'));

        $data = $this->service->getPositionsByDate(1421444, 1, $date);

        return response()->json([
            'status_code' => 200,
            'message' => 'ok',
            'data' => $data,
        ]);
    }
}

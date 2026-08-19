<?php

namespace App\Http\Controllers;

use App\Services\Streaming\StreamingService;
use Illuminate\Http\JsonResponse;

class StreamStatusController extends Controller
{
    public function __invoke(StreamingService $streaming): JsonResponse
    {
        $status = $streaming->status();

        // Salva histórico quando há metadados
        if (! empty($status['metadata'])) {
            $streaming->saveHistory($status['metadata']);
        }

        return response()->json($status);
    }
}
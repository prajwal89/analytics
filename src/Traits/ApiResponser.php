<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Traits;

trait ApiResponser
{
    protected function successResponse(array $data = [], $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(string $message, int $code)
    {
        return response()->json([
            'status' => 'fail',
            'message' => $message,
            'data' => null,
        ], $code);
    }
}

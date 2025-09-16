<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return success response
     * (Check Campaign controller for demo)
     *
     * @param mixed $data
     * @param array|string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data = null, $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errCode' => 0,
        ], $code);
    }

    /**
     * Return error response
     *
     * @param array|string|null $message
     * @param int $code
     * @param array $customPayload
     * @return JsonResponse
     */
    protected function errorResponse($message = null, int $code = 200, $customPayload = []): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errCode' => 1,
        ];

        if (!empty($customPayload)) {
            $data['customPayload'] = $customPayload;
        }

        return response()->json($data, $code);
    }
}

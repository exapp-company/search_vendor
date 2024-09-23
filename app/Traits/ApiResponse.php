<?php

namespace App\Traits;

use App\Enums\HttpStatus;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{


    public function success(string $message, int $code = HttpStatus::ok): JsonResponse
    {
        return $this->response($message, $code);
    }


    public function error(string $message, int $code = HttpStatus::badRequest): JsonResponse
    {
        return $this->response($message, $code, 'error');
    }


    public function info(string $message, int $code = HttpStatus::ok): JsonResponse
    {
        return $this->response($message, $code, 'info');
    }


    private function response(string $message, int $code, string $status = ''): JsonResponse
    {
        $responseData = [
            'message' => $message,
        ];

        if ($status === 'error') {
            $responseData['status'] = 'error';
        }

        if ($status === 'info') {
            $responseData['status'] = 'info';
        }


        return response()->json($responseData, $code);
    }
}

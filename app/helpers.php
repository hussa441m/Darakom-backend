<?php

use App\Helpers\ApiResponse;

if (!function_exists('apiSuccess')) {
    function apiSuccess(string $message = 'تمت العملية بنجاح', $data = null, int $statusCode = 200)
    {
        return ApiResponse::success($message, $data, $statusCode);
    }
}

if (!function_exists('apiError')) {
    function apiError(string $message = 'An error occurred', $errors = null, int $statusCode = 400)
    {
        return ApiResponse::error($message, $errors, $statusCode);
    }
}

if (!function_exists('apiUnauthorized')) {
    function apiUnauthorized(string $message = 'معلومات الحساب غير صحيحة')
    {
        return ApiResponse::unauthorized($message );
    }
}




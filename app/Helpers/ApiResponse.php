<?php

namespace App\Helpers;


use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(string $message , $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
    
    public static function error(string $message = 'حدث خطأ', $errors = null, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
  
    
    public static function unauthorized(string $message): JsonResponse
    {
        return self::error($message, null, 401);
    }    
}
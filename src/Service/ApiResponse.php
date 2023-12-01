<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{
    /**
     * @return JsonResponse
     */
    public function notFound(): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'message' => "Not Found"], 400);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function success(array $data): JsonResponse
    {
        $response = ['status' => 'success', 'data' => $data, 'total'=>count($data)];
        return new JsonResponse($response);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function error(string $message): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'message' => $message], 500);
    }

    /**
     * @param object $exception
     * @return JsonResponse
     */
    public function errorException(object $exception): JsonResponse
    {
        return $this->error($exception->getMessage());
    }


}

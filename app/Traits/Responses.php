<?php

namespace App\Traits;

trait Responses
{
    /**
     * Return a standardized success response
     *
     * @param string $message Success message
     * @param array $data Response data
     * @param int $status HTTP status code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse(string $message, array $data = [], int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Return a standardized error response
     *
     * @param string $message Error message
     * @param array $errors Validation or error details
     * @param int $status HTTP status code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message, array $errors = [], int $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * Return a standardized paginated response
     *
     * @param string $message Success message
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param int $status HTTP status code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function paginatedResponse(string $message, $paginator, int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'items' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                ]
            ]
        ], $status);
    }
}

<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Returns a successful response with the given data, message and code.
     *
     * @param mixed $data The data to be returned.
     * @param string $message The message to be returned.
     * @param int $code The HTTP status code to be returned.
     * @return JsonResponse
     */
    protected function successResponse(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Returns a failed response with the given message, errors and code.
     *
     * @param string $message The message to be returned.
     * @param mixed $errors The errors to be returned.
     * @param int $code The HTTP status code to be returned.
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Something went wrong', mixed $errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Returns a successful response with the given paginated data, message and code.
     *
     * @param \Illuminate\Contracts\Pagination\Paginator $paginator The paginator instance.
     * @param string $message The message to be returned.
     * @param int $code The HTTP status code to be returned.
     * @return JsonResponse
     * 
     * Example:
     * return $this->paginatedResponse($paginator, 'Success', 200);
     */
    protected function paginatedResponse($paginator, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ]
        ], $code);
    }
}

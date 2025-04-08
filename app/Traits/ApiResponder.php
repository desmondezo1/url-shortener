<?php

namespace App\Traits;

trait ApiResponder
{
    /**
     * Return a success JSON response
     *
     * @param array|string $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return an error JSON response
     *
     * @param string $message
     * @param int $code
     * @param array|string|null $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $code, $data = null)
    {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * URL shortener specific success response
     *
     * @param string $originalUrl
     * @param string $shortUrl
     * @return \Illuminate\Http\JsonResponse
     */
    protected function urlEncodedResponse($originalUrl, $shortUrl)
    {
        return $this->successResponse([
            'original_url' => $originalUrl,
            'short_url' => $shortUrl
        ]);
    }

    /**
     * URL decoder specific success response
     *
     * @param string $shortUrl
     * @param string $originalUrl
     * @return \Illuminate\Http\JsonResponse
     */
    protected function urlDecodedResponse($shortUrl, $originalUrl)
    {
        return $this->successResponse([
            'short_url' => $shortUrl,
            'original_url' => $originalUrl
        ]);
    }
}

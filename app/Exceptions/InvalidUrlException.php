<?php

namespace App\Exceptions;

use Exception;

class InvalidUrlException extends Exception
{
    /**
     * Create a new InvalidUrlException instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @return void
     */
    public function __construct($message = 'Invalid URL format', $code = 400, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     *
     * @return bool
     */
    public function report()
    {
        // Log the invalid URL attempt if needed
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'error' => $this->getMessage()
        ], $this->getCode());
    }
}

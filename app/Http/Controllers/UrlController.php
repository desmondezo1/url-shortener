<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\UrlServiceInterface;
use App\Exceptions\InvalidUrlException;
use App\Exceptions\UrlNotFoundException;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    use ApiResponder;

    protected $urlService;

    public function __construct(UrlServiceInterface $urlService)
    {
        $this->urlService = $urlService;
    }

    public function encode(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|string'
        ]);

        try {
            $url = $request->input('url');
            $shortUrl = $this->urlService->encode($url);

            return $this->urlEncodedResponse($url, $shortUrl);
        } catch (InvalidUrlException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while encoding the URL', 500);
        }
    }

    public function decode(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|string'
        ]);

        try {
            $shortUrl = $request->input('url');
            $originalUrl = $this->urlService->decode($shortUrl);

            return $this->urlDecodedResponse($shortUrl, $originalUrl);
        } catch (UrlNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while decoding the URL', 500);
        }
    }

    /**
     * Redirect a short URL to its original destination
     *
     * @param string $code
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function redirect($code)
    {
        try {
            $shortUrl = config('url.base_url') . $code;
            $originalUrl = $this->urlService->decode($shortUrl);

            return redirect($originalUrl);
        } catch (UrlNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred', 500);
        }
    }
}

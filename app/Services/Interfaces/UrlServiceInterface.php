<?php

namespace App\Services\Interfaces;

/**
 * Interface for URL shortening service
 */
interface UrlServiceInterface
{
    /**
     * Encodes a long URL into a shortened URL
     *
     * @param string $url The original URL to be shortened
     * @return string The shortened URL
     * @throws \App\Exceptions\InvalidUrlException When the URL format is invalid
     */
    public function encode($url);

    /**
     * Decodes a shortened URL back to its original form
     *
     * @param string $shortUrl The shortened URL to be decoded
     * @return string The original URL
     * @throws \App\Exceptions\UrlNotFoundException When the short URL is not found
     */
    public function decode($shortUrl);
}

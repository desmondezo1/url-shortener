<?php

namespace App\Services;

use App\Exceptions\InvalidUrlException;
use App\Exceptions\UrlNotFoundException;
use App\Services\Interfaces\UrlServiceInterface;
use App\Services\Validation\UrlValidator;
use App\Services\Encoding\Base62Encoder;
use App\Services\Storage\InMemoryStore;
use App\Services\Cache\CacheManager;

/**
 * Main service for URL shortening and expanding
 */
class UrlService implements UrlServiceInterface
{
    /**
     * URL validator instance
     *
     * @var \App\Services\Validation\UrlValidator
     */
    private $validator;

    /**
     * URL encoder instance
     *
     * @var \App\Services\Encoding\Base62Encoder
     */
    private $encoder;

    /**
     * Storage instance
     *
     * @var \App\Services\Storage\InMemoryStore
     */
    private $store;

    /**
     * Cache manager instance
     *
     * @var \App\Services\Cache\CacheManager
     */
    private $cache;

    /**
     * Base URL for shortened URLs
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Maximum number of collision resolution attempts
     *
     * @var int
     */
    private $maxCollisionAttempts = 5;

    /**
     * UrlService constructor
     *
     * @param \App\Services\Validation\UrlValidator $validator
     * @param \App\Services\Encoding\Base62Encoder $encoder
     * @param \App\Services\Storage\InMemoryStore $store
     * @param \App\Services\Cache\CacheManager $cache
     * @param string|null $baseUrl Base URL for shortened URLs (null will use app URL)
     */
    public function __construct(
        UrlValidator $validator,
        Base62Encoder $encoder,
        InMemoryStore $store,
        CacheManager $cache,
        $baseUrl = null
    ) {
        $this->validator = $validator;
        $this->encoder = $encoder;
        $this->store = $store;
        $this->cache = $cache;
        $this->baseUrl = $baseUrl ? rtrim($baseUrl, '/') . '/' : rtrim(url('/'), '/') . '/';
    }

    /**
     * Encode a URL to a shortened URL
     *
     * @param string $url The URL to shorten
     * @return string The shortened URL
     * @throws \App\Exceptions\InvalidUrlException When the URL is invalid
     */
    public function encode($url)
    {
        // Validate the URL
        if (!$this->validator->validate($url)) {
            throw new InvalidUrlException('Invalid URL format');
        }

        // Normalize the URL
        $url = $this->validator->normalize($url);

        // Check if the URL is already in cache
        $cachedShortCode = $this->cache->findByOriginalUrl($url);
        if ($cachedShortCode) {
            return $this->buildShortUrl($cachedShortCode);
        }

        // Check if the URL is already in the store
        $existingShortCode = $this->store->findByOriginalUrl($url);
        if ($existingShortCode) {
            // Cache the result for faster future lookups
            $this->cache->cacheBidirectional($url, $existingShortCode);
            return $this->buildShortUrl($existingShortCode);
        }

        // Generate a new short code
        $shortCode = $this->generateUniqueShortCode($url);

        // Save the mapping
        $this->store->save($url, $shortCode);

        // Cache the mapping
        $this->cache->cacheBidirectional($url, $shortCode);

        return $this->buildShortUrl($shortCode);
    }

    /**
     * Decode a shortened URL back to its original URL
     *
     * @param string $shortUrl The shortened URL to decode
     * @return string The original URL
     * @throws \App\Exceptions\UrlNotFoundException When the short URL is not found
     */
    public function decode($shortUrl)
    {
        // Extract the short code from the URL
        $shortCode = $this->extractShortCode($shortUrl);

        // Check if the short code is in cache
        $cachedOriginalUrl = $this->cache->findByShortCode($shortCode);
        if ($cachedOriginalUrl) {
            return $cachedOriginalUrl;
        }

        // Check if the short code is in the store
        $originalUrl = $this->store->findByShortCode($shortCode);
        if (!$originalUrl) {
            throw new UrlNotFoundException('Short URL not found');
        }

        // Cache the result for faster future lookups
        $this->cache->cacheBidirectional($originalUrl, $shortCode);

        return $originalUrl;
    }

    /**
     * Generate a unique short code for a URL with collision handling
     *
     * @param string $url The URL to encode
     * @return string A unique short code
     * @throws \RuntimeException When unable to generate a unique code after max attempts
     */
    private function generateUniqueShortCode($url)
    {
        // Try the basic encoding first
        $shortCode = $this->encoder->encode($url);

        // If no collision, return the code
        if (!$this->store->checkCollision($shortCode)) {
            return $shortCode;
        }

        // Handle collisions by adding salt
        for ($attempt = 1; $attempt <= $this->maxCollisionAttempts; $attempt++) {
            $shortCode = $this->encoder->generateWithIncrement($url, $attempt);

            if (!$this->store->checkCollision($shortCode)) {
                return $shortCode;
            }
        }

        // If we reach here, we couldn't find a unique code after max attempts
        throw new \RuntimeException('Unable to generate a unique short code after maximum attempts');
    }

    /**
     * Build a complete short URL from a short code
     *
     * @param string $shortCode The short code
     * @return string The complete short URL
     */
    private function buildShortUrl($shortCode)
    {
        return $this->baseUrl . $shortCode;
    }

    /**
     * Extract the short code from a short URL
     *
     * @param string $shortUrl The short URL
     * @return string The extracted short code
     */
    private function extractShortCode($shortUrl)
    {
        // Remove the base URL if present
        if (strpos($shortUrl, $this->baseUrl) === 0) {
            return substr($shortUrl, strlen($this->baseUrl));
        }

        // Otherwise, just get the last path segment
        $parts = explode('/', rtrim($shortUrl, '/'));
        return end($parts);
    }
}

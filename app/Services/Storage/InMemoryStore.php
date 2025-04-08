<?php

namespace App\Services\Storage;

/**
 * In-memory storage for URL mappings
 */
class InMemoryStore
{
    /**
     * Maps from original URLs to short codes
     *
     * @var array
     */
    private $urlToCode = [];

    /**
     * Maps from short codes to original URLs
     *
     * @var array
     */
    private $codeToUrl = [];

    /**
     * Find a short code for a given original URL
     *
     * @param string $url The original URL
     * @return string|null The short code if found, null otherwise
     */
    public function findByOriginalUrl($url)
    {
        return $this->urlToCode[$url] ?? null;
    }

    /**
     * Find an original URL for a given short code
     *
     * @param string $code The short code
     * @return string|null The original URL if found, null otherwise
     */
    public function findByShortCode($code)
    {
        return $this->codeToUrl[$code] ?? null;
    }

    /**
     * Save a mapping between an original URL and a short code
     *
     * @param string $url The original URL
     * @param string $code The short code
     * @return bool Success indicator
     */
    public function save($url, $code)
    {
        $this->urlToCode[$url] = $code;
        $this->codeToUrl[$code] = $url;

        return true;
    }

    /**
     * Check if a short code already exists (collision detection)
     *
     * @param string $code The short code to check
     * @return bool True if the code exists, false otherwise
     */
    public function checkCollision($code)
    {
        return isset($this->codeToUrl[$code]);
    }

    /**
     * Get the total number of stored URL mappings
     *
     * @return int Count of stored mappings
     */
    public function count()
    {
        return count($this->codeToUrl);
    }

    /**
     * Clear all stored mappings
     *
     * @return void
     */
    public function clear()
    {
        $this->urlToCode = [];
        $this->codeToUrl = [];
    }
}

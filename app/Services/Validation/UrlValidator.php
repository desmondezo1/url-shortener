<?php

namespace App\Services\Validation;

/**
 * Validates URLs for the shortening service
 */
class UrlValidator
{
    /**
     * Validates if a string is a properly formatted URL
     *
     * @param string $url The URL to validate
     * @return bool True if the URL is valid, false otherwise
     */
    public function validate($url)
    {
        // Check if the URL is not empty
        if (empty($url)) {
            return false;
        }

        // Use PHP's filter_var function to validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check if the URL has an allowed scheme
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], ['http', 'https'])) {
            return false;
        }

        // Check if the URL has a host
        if (!isset($parsedUrl['host'])) {
            return false;
        }

        return true;
    }

    /**
     * Normalizes a URL by removing trailing slashes and standardizing format
     *
     * @param string $url The URL to normalize
     * @return string The normalized URL
     */
    public function normalize($url)
    {
        // Remove trailing slashes
        $url = rtrim($url, '/');

        // Ensure the URL has a scheme
        if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
            $url = 'http://' . $url;
        }

        return $url;
    }
}

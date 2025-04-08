<?php

namespace App\Services\Cache;

use Illuminate\Contracts\Cache\Repository as Cache;

class CacheManager
{
    private $cache;
    private $ttl;

    /**
     * Prefix for URL keys in cache
     */
    private const URL_PREFIX = 'url:';

    /**
     * Prefix for code keys in cache
     */
    private const CODE_PREFIX = 'code:';

    public function __construct(Cache $cache, $ttl = 3600)
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    public function findByOriginalUrl($url)
    {
        return $this->cache->get($this->getUrlKey($url));
    }

    public function findByShortCode($code)
    {
        return $this->cache->get($this->getCodeKey($code));
    }

    public function cache($key, $value, $ttl = null)
    {
        $ttl = $ttl ?? $this->ttl;

        if (strpos($key, 'http') === 0) {
            $this->cache->put($this->getUrlKey($key), $value, $ttl);
        } else {
            $this->cache->put($this->getCodeKey($key), $value, $ttl);
        }

        return true;
    }

    // In app/Services/Cache/CacheManager.php, add this method:

    /**
     * Cache both mappings at once (URL->code and code->URL)
     *
     * @param string $url The original URL
     * @param string $code The short code
     * @param int|null $ttl Time-to-live in seconds (null = use default)
     * @return bool Success indicator
     */
    public function cacheBidirectional($url, $code, $ttl = null)
    {
        $ttl = $ttl ?? $this->ttl;

        $urlResult = $this->cache->put($this->getUrlKey($url), $code, $ttl);
        $codeResult = $this->cache->put($this->getCodeKey($code), $url, $ttl);

        return $urlResult && $codeResult;
    }


    /**
     * Get the cache key for a URL
     *
     * @param string $url The URL
     * @return string The cache key
     */
    private function getUrlKey($url)
    {
        return self::URL_PREFIX . md5($url);
    }

    /**
     * Get the cache key for a short code
     *
     * @param string $code The short code
     * @return string The cache key
     */
    private function getCodeKey($code)
    {
        return self::CODE_PREFIX . $code;
    }
}

<?php

namespace Tests\Unit;

use App\Services\Cache\CacheManager;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Tests\TestCase;

class CacheManagerTest extends TestCase
{
    protected $cacheManager;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a cache manager with an array-based cache for testing
        $arrayCache = new Repository(new ArrayStore());
        $this->cacheManager = new CacheManager($arrayCache, 60);
    }

    /** @test */
    public function it_caches_and_retrieves_by_url()
    {
        $url = 'https://example.com';
        $code = 'abc123';

        $this->cacheManager->cache($url, $code);

        $this->assertEquals($code, $this->cacheManager->findByOriginalUrl($url));
    }

    /** @test */
    public function it_caches_and_retrieves_by_code()
    {
        $url = 'https://example.com';
        $code = 'abc123';

        $this->cacheManager->cache($code, $url);

        $this->assertEquals($url, $this->cacheManager->findByShortCode($code));
    }

    /** @test */
    public function it_caches_bidirectionally()
    {
        $url = 'https://example.com';
        $code = 'abc123';

        $this->cacheManager->cacheBidirectional($url, $code);

        $this->assertEquals($code, $this->cacheManager->findByOriginalUrl($url));
        $this->assertEquals($url, $this->cacheManager->findByShortCode($code));
    }

    /** @test */
    public function it_returns_null_for_nonexistent_cache_items()
    {
        $this->assertNull($this->cacheManager->findByOriginalUrl('https://nonexistent.com'));
        $this->assertNull($this->cacheManager->findByShortCode('nonexistent'));
    }
}

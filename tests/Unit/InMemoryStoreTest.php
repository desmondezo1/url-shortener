<?php

namespace Tests\Unit;

use App\Services\Storage\InMemoryStore;
use Tests\TestCase;

class InMemoryStoreTest extends TestCase
{
    /** @test */
    public function it_saves_and_retrieves_url_mappings()
    {
        $store = new InMemoryStore();
        $url = 'https://example.com';
        $code = 'abc123';

        $store->save($url, $code);

        $this->assertEquals($code, $store->findByOriginalUrl($url));
        $this->assertEquals($url, $store->findByShortCode($code));
    }

    /** @test */
    public function it_returns_null_for_nonexistent_mappings()
    {
        $store = new InMemoryStore();

        $this->assertNull($store->findByOriginalUrl('https://nonexistent.com'));
        $this->assertNull($store->findByShortCode('nonexistent'));
    }

    /** @test */
    public function it_detects_collisions()
    {
        $store = new InMemoryStore();
        $code = 'abc123';

        $store->save('https://example1.com', $code);

        $this->assertTrue($store->checkCollision($code));
        $this->assertFalse($store->checkCollision('xyz789'));
    }

    /** @test */
    public function it_clears_all_mappings()
    {
        $store = new InMemoryStore();

        $store->save('https://example.com', 'abc123');
        $this->assertEquals(1, $store->count());

        $store->clear();
        $this->assertEquals(0, $store->count());
    }
}

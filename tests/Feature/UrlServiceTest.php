<?php

namespace Tests\Feature;

use App\Exceptions\InvalidUrlException;
use App\Exceptions\UrlNotFoundException;
use App\Services\Cache\CacheManager;
use App\Services\Encoding\Base62Encoder;
use App\Services\Storage\InMemoryStore;
use App\Services\UrlService;
use App\Services\Validation\UrlValidator;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Tests\TestCase;

class UrlServiceTest extends TestCase
{
    protected $urlService;
    protected $store;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = new UrlValidator();
        $encoder = new Base62Encoder();
        $this->store = new InMemoryStore();
        $arrayCache = new Repository(new ArrayStore());
        $cacheManager = new CacheManager($arrayCache, 60);

        // Use a test base URL for consistency in tests
        $baseUrl = url('/');

        $this->urlService = new UrlService(
            $validator,
            $encoder,
            $this->store,
            $cacheManager,
            $baseUrl
        );
    }

    /** @test */
    public function it_encodes_a_valid_url()
    {
        $url = 'https://example.com/path';
        $shortUrl = $this->urlService->encode($url);

        $baseUrl = url('/');
        $this->assertStringStartsWith($baseUrl, $shortUrl);

        // Calculate the expected length
        $expectedLength = strlen($baseUrl) + 1 + 6; // Base URL + / + 6 char code
        $this->assertEquals($expectedLength, strlen($shortUrl));
    }

    /** @test */
    public function it_throws_exception_for_invalid_url()
    {
        $this->expectException(InvalidUrlException::class);

        $this->urlService->encode('not-a-valid-url');
    }

    /** @test */
    public function it_returns_same_short_url_for_same_input()
    {
        $url = 'https://example.com/consistent';

        $firstShortUrl = $this->urlService->encode($url);
        $secondShortUrl = $this->urlService->encode($url);

        $this->assertEquals($firstShortUrl, $secondShortUrl);
    }

    /** @test */
    public function it_decodes_a_short_url_to_original()
    {
        $originalUrl = 'https://example.com/decode-test';
        $shortUrl = $this->urlService->encode($originalUrl);

        $decodedUrl = $this->urlService->decode($shortUrl);

        $this->assertEquals($originalUrl, $decodedUrl);
    }

    /** @test */
    public function it_throws_exception_for_nonexistent_short_url()
    {
        $this->expectException(UrlNotFoundException::class);

        $this->urlService->decode(url('/') . '/NONEXISTENT');
    }

    /** @test */
    public function it_handles_urls_with_special_characters()
    {
        $originalUrl = 'https://example.com/path?param=value&special=%20%21';
        $shortUrl = $this->urlService->encode($originalUrl);
        $decodedUrl = $this->urlService->decode($shortUrl);

        $this->assertEquals($originalUrl, $decodedUrl);
    }

    /** @test */
    public function it_handles_hash_collisions_correctly()
    {
        // Create a mock encoder that always returns the same short code initially
        // but handles incremental encoding correctly
        $mockEncoder = \Mockery::mock(Base62Encoder::class);
        $mockEncoder->shouldReceive('encode')
            ->withAnyArgs()
            ->andReturn('SAME01'); // Always return the same code initially

        $mockEncoder->shouldReceive('generateWithIncrement')
            ->withAnyArgs()
            ->andReturn('DIFF01'); // Different code on increment attempt

        // Create a mock store that simulates a collision on the first code
        $mockStore = \Mockery::mock(InMemoryStore::class);
        $mockStore->shouldReceive('findByOriginalUrl')
            ->andReturn(null); // No existing URLs

        $mockStore->shouldReceive('checkCollision')
            ->with('SAME01')
            ->once()
            ->andReturn(false); // First time no collision

        $mockStore->shouldReceive('checkCollision')
            ->with('SAME01')
            ->once()
            ->andReturn(true); // Second time has collision

        $mockStore->shouldReceive('checkCollision')
            ->with('DIFF01')
            ->andReturn(false); // No collision with the different code

        $mockStore->shouldReceive('save')
            ->withAnyArgs()
            ->andReturn(true); // All saves succeed

        $mockStore->shouldReceive('findByShortCode')
            ->with('SAME01')
            ->andReturn('https://example.com/first');

        $mockStore->shouldReceive('findByShortCode')
            ->with('DIFF01')
            ->andReturn('https://example.com/second');

        // Create a real cache manager for simplicity
        $arrayCache = new Repository(new ArrayStore());
        $cacheManager = new CacheManager($arrayCache, 60);

        // Create a new service with our mocks
        $validator = new UrlValidator();
        $baseUrl = url('/');

        $urlService = new UrlService(
            $validator,
            $mockEncoder,
            $mockStore,
            $cacheManager,
            $baseUrl
        );

        // First URL will claim the 'SAME01' code
        $firstUrl = 'https://example.com/first';
        $firstShortUrl = $urlService->encode($firstUrl);

        // Verify the first URL got the initial code
        $this->assertEquals($baseUrl . '/SAME01', $firstShortUrl);

        // Now encode a second URL, which should cause a collision
        // and use the incremented code instead
        $secondUrl = 'https://example.com/second';
        $secondShortUrl = $urlService->encode($secondUrl);

        // Verify the second URL got the incremented code
        $this->assertEquals($baseUrl . '/DIFF01', $secondShortUrl);

        // Verify both URLs decode correctly
        $this->assertEquals($firstUrl, $urlService->decode($firstShortUrl));
        $this->assertEquals($secondUrl, $urlService->decode($secondShortUrl));
    }
}

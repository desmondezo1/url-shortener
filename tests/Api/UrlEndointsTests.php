<?php

namespace Tests\Api;

use Tests\TestCase;

class UrlEndpointsTest extends TestCase
{
    /** @test */
    public function encode_endpoint_returns_a_shortened_url()
    {
        $this->post('/encode', [
            'url' => 'https://example.com/very/long/path/with/parameters?q=test'
        ]);

        $this->assertResponseStatus(200);

        // Check JSON structure
        $this->seeJsonStructure([
            'original_url',
            'short_url'
        ]);

        // Check content
        $response = json_decode($this->response->getContent(), true);
        $this->assertEquals('https://example.com/avery/long/path/withplenty/parameters?q=test', $response['original_url']);
        $this->assertStringStartsWith('http://short.est/', $response['short_url']);
    }

    /** @test */
    public function encode_endpoint_returns_error_for_invalid_url()
    {
        $this->post('/encode', [
            'url' => 'not-a-valid-url'
        ]);

        $this->assertResponseStatus(400);

        $this->seeJsonStructure([
            'error'
        ]);
    }

    /** @test */
    public function decode_endpoint_returns_original_url()
    {
        // First, create a short URL
        $this->post('/encode', [
            'url' => 'https://example.com/decode/test/path'
        ]);

        $encodeResponse = json_decode($this->response->getContent(), true);
        $shortUrl = $encodeResponse['short_url'];

        // Then, decode it
        $this->post('/decode', [
            'url' => $shortUrl
        ]);

        $this->assertResponseStatus(200);

        $this->seeJsonStructure([
            'short_url',
            'original_url'
        ]);

        $decodeResponse = json_decode($this->response->getContent(), true);
        $this->assertEquals('https://example.com/decode/test/path', $decodeResponse['original_url']);
    }

    /** @test */
    public function decode_endpoint_returns_error_for_unknown_url()
    {
        $this->post('/decode', [
            'url' => 'http://short.est/UNKNOWN'
        ]);

        $this->assertResponseStatus(404);

        $this->seeJsonStructure([
            'error'
        ]);
    }

    /** @test */
    public function it_handles_full_encode_decode_cycle_with_special_characters()
    {
        $originalUrl = 'https://example.com/path?q=special%20chars&filter=a+b+c#section';

        // Encode
        $this->post('/encode', ['url' => $originalUrl]);
        $encodeResponse = json_decode($this->response->getContent(), true);

        // Decode
        $this->post('/decode', ['url' => $encodeResponse['short_url']]);
        $decodeResponse = json_decode($this->response->getContent(), true);

        $this->assertEquals($originalUrl, $decodeResponse['original_url']);
    }

    /** @test */
    public function it_returns_a_shortened_url_for_valid_input()
    {
        $response = $this->post('/api/shorten', [
            'url' => 'https://example.com/very/long/url'
        ]);

        $response->assertResponseStatus(200);
        $response = json_decode($response->response->getContent(), true);

        $this->assertArrayHasKey('short_url', $response);
        $this->assertArrayHasKey('original_url', $response);
        $this->assertEquals('https://example.com/very/long/url', $response['original_url']);
        $this->assertStringStartsWith(url('/'), $response['short_url']);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_short_url()
    {
        $response = $this->post('/api/expand', [
            'url' => url('/') . '/UNKNOWN'
        ]);

        $response->assertResponseStatus(404);
    }
}

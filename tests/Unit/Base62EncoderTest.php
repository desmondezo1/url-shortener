<?php

namespace Tests\Unit;

use App\Services\Encoding\Base62Encoder;
use Tests\TestCase;

class Base62EncoderTest extends TestCase
{
    protected $encoder;
    protected const DEFAULT_CODE_LENGTH = 6;

    protected function setUp(): void
    {
        parent::setUp();
        $this->encoder = new Base62Encoder();
    }

    /** @test */
    public function it_generates_a_six_character_alphanumeric_string()
    {
        $url = 'https://example.com';
        $shortCode = $this->encoder->encode($url);

        // Check length
        $this->assertEquals(self::DEFAULT_CODE_LENGTH, strlen($shortCode));

        // Check it's alphanumeric
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z]+$/', $shortCode);
    }

    /** @test */
    public function it_is_a_correct_base62_encoding()
    {
        $url = 'https://example.com';
        $shortCode = $this->encoder->encode($url);

        // Check that it only contains Base62 characters (0-9, a-z, A-Z)
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z]+$/', $shortCode);

        // Check that different parts of the code use different characters from the Base62 set
        $charactersUsed = array_unique(str_split($shortCode));
        $this->assertGreaterThan(1, count($charactersUsed), 'Should use multiple characters from Base62 set');
    }

    /** @test */
    public function it_handles_empty_string_appropriately()
    {
        // It should still return a code for empty string, as it's just doing a hash
        $emptyStringCode = $this->encoder->encode('');

        // Check it's still a valid Base62 string of correct length
        $this->assertEquals(self::DEFAULT_CODE_LENGTH, strlen($emptyStringCode));
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z]+$/', $emptyStringCode);
    }

    /** @test */
    public function it_encodes_strings_with_special_characters()
    {
        $url = 'https://example.com/path?param=value&special=%20%21';
        $shortCode = $this->encoder->encode($url);

        // Check it's a valid code
        $this->assertEquals(self::DEFAULT_CODE_LENGTH, strlen($shortCode));
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z]+$/', $shortCode);
    }

    /** @test */
    public function it_always_returns_same_output_for_same_input()
    {
        $url = 'https://example.com/consistent';

        $firstCode = $this->encoder->encode($url);
        $secondCode = $this->encoder->encode($url);

        $this->assertEquals($firstCode, $secondCode);
    }

    /** @test */
    public function it_produces_different_output_for_different_inputs()
    {
        $url1 = 'https://example.com/page1';
        $url2 = 'https://example.com/page2';

        $code1 = $this->encoder->encode($url1);
        $code2 = $this->encoder->encode($url2);

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    public function it_generates_different_codes_with_increments()
    {
        $url = 'https://example.com/collision';

        $originalCode = $this->encoder->encode($url);
        $incrementedCode = $this->encoder->generateWithIncrement($url, 1);

        $this->assertNotEquals($originalCode, $incrementedCode);
    }

    /** @test */
    public function it_uses_custom_length_when_specified()
    {
        $url = 'https://example.com';
        $customLength = 8;

        $shortCode = $this->encoder->encode($url, '', $customLength);

        $this->assertEquals($customLength, strlen($shortCode));
    }
}

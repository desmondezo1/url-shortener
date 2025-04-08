<?php

namespace App\Services\Encoding;

class Base62Encoder
{
    private const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const BASE = 62;
    private const DEFAULT_CODE_LENGTH = 6;

    /**
     * Encodes a URL or string into a short code
     *
     * @param string $input The input to encode (typically a URL)
     * @param string $salt Optional salt to add for collision resolution
     * @param int $length Length of the resulting code
     * @return string The encoded short code
     */
    public function encode($input, $salt = '', $length = self::DEFAULT_CODE_LENGTH)
    {
        // Create a unique hash from the input and salt
        $hash = md5($input . $salt);

        // Use a different approach to prevent integer overflow
        $result = '';
        $chars = str_split($hash);

        // Process the hash in smaller chunks to generate the base62 string
        for ($i = 0; $i < 16 && strlen($result) < $length; $i++) {
            $chunk = substr($hash, $i * 2, 2);
            $decimal = hexdec($chunk);

            // Convert this chunk to base62
            $chunkResult = '';
            while ($decimal > 0) {
                $remainder = $decimal % self::BASE;
                $chunkResult = self::CHARACTERS[$remainder] . $chunkResult;
                $decimal = (int)($decimal / self::BASE);
            }

            $result .= $chunkResult;
        }

        // Ensure we have the right length
        if (strlen($result) < $length) {
            // Pad with characters derived from the hash
            for ($i = 0; $i < strlen($hash) && strlen($result) < $length; $i++) {
                $charIndex = hexdec($hash[$i]) % self::BASE;
                $result .= self::CHARACTERS[$charIndex];
            }
        }

        // Take only the first {$length} characters
        return substr($result, 0, $length);
    }

    /**
     * Generates a new short code with a numeric increment for collision resolution
     *
     * @param string $input The original input
     * @param int $attempt The attempt number for collision resolution
     * @param int $length Length of the resulting code
     * @return string A new short code
     */
    public function generateWithIncrement($input, $attempt, $length = self::DEFAULT_CODE_LENGTH)
    {
        return $this->encode($input, strval($attempt), $length);
    }
}

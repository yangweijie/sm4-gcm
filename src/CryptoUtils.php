<?php

declare(strict_types=1);

namespace yangweijie\SM4GCM;

/**
 * Helper functions for data conversion and validation.
 */
class CryptoUtils
{
    /**
     * Convert bytes to hexadecimal string.
     *
     * @param string $bytes The bytes to convert
     * @return string The hexadecimal representation
     */
    public static function toHex(string $bytes): string
    {
        return bin2hex($bytes);
    }

    /**
     * Convert hexadecimal string to bytes.
     *
     * @param string $hex The hexadecimal string to convert
     * @return string The byte representation
     * @throws \InvalidArgumentException If the hex string is invalid
     */
    public static function toBytes(string $hex): string
    {
        if (strlen($hex) % 2 !== 0) {
            throw new \InvalidArgumentException("Invalid hex string length");
        }
        
        $bytes = hex2bin($hex);
        if ($bytes === false) {
            throw new \InvalidArgumentException("Invalid hex string");
        }
        
        return $bytes;
    }

    /**
     * Generate cryptographically secure random bytes.
     *
     * @param int $length The number of bytes to generate
     * @return string The random bytes
     * @throws \Exception If unable to generate random bytes
     */
    public static function secureRandom(int $length): string
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException("Length must be positive");
        }
        
        return random_bytes($length);
    }

    /**
     * Compare two strings in constant time to prevent timing attacks.
     *
     * @param string $a First string to compare
     * @param string $b Second string to compare
     * @return bool True if strings are equal, false otherwise
     */
    public static function constantTimeEquals(string $a, string $b): bool
    {
        return hash_equals($a, $b);
    }
}
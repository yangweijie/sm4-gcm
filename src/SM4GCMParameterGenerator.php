<?php

declare(strict_types=1);

namespace yangweijie\SM4GCM;

use yangweijie\SM4GCM\Exceptions\InvalidParameterException;

/**
 * Secure parameter generation for SM4-GCM.
 */
class SM4GCMParameterGenerator
{
    public const DEFAULT_IV_LENGTH = 12; // 96 bits
    public const DEFAULT_TAG_LENGTH = 128; // 128 bits

    /**
     * Generate a cryptographically secure random IV (nonce).
     *
     * @param int $length The length of the IV in bytes (default: 12 bytes)
     * @return string The generated IV
     * @throws \Exception If unable to generate random bytes
     */
    public function generateIV(int $length = self::DEFAULT_IV_LENGTH): string
    {
        if ($length <= 0) {
            throw new InvalidParameterException("IV length must be positive");
        }
        
        return CryptoUtils::secureRandom($length);
    }

    /**
     * Generate SM4GCMParameters with cryptographically secure random values.
     *
     * @param int $tagLength The authentication tag length in bits (default: 128)
     * @param int|null $ivLength The IV length in bytes (default: 12)
     * @return SM4GCMParameters The generated parameters
     * @throws \Exception If unable to generate random bytes
     * @throws InvalidParameterException If parameters are invalid
     */
    public function generateParameters(
        int $tagLength = self::DEFAULT_TAG_LENGTH,
        ?int $ivLength = null
    ): SM4GCMParameters {
        if ($ivLength === null) {
            $ivLength = self::DEFAULT_IV_LENGTH;
        }
        
        $iv = $this->generateIV($ivLength);
        return new SM4GCMParameters($iv, $tagLength);
    }
}
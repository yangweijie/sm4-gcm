<?php

declare(strict_types=1);

namespace yangweijie\SM4GCM;

use yangweijie\SM4GCM\Exceptions\InvalidParameterException;

/**
 * Utility class for validating SM4-GCM parameters.
 */
class SM4GCMParameterValidator
{
    public const SM4_KEY_SIZE = 16; // 128 bits
    public const GCM_NONCE_MIN_SIZE = 1;
    public const GCM_NONCE_MAX_SIZE = 128;
    public const GCM_DEFAULT_NONCE_SIZE = 12; // 96 bits
    public const GCM_TAG_SIZE = 16; // 128 bits

    /**
     * Validate the key for SM4-GCM encryption/decryption.
     *
     * @param string $key The key to validate
     * @throws InvalidParameterException If the key is invalid
     */
    public static function validateKey(string $key): void
    {
        if (strlen($key) !== self::SM4_KEY_SIZE) {
            throw new InvalidParameterException(
                sprintf(
                    "Invalid key size. Expected %d bytes, got %d bytes",
                    self::SM4_KEY_SIZE,
                    strlen($key)
                )
            );
        }
    }

    /**
     * Validate the nonce for SM4-GCM encryption/decryption.
     *
     * @param string $nonce The nonce to validate
     * @throws InvalidParameterException If the nonce is invalid
     */
    public static function validateNonce(string $nonce): void
    {
        $nonceLength = strlen($nonce);
        if ($nonceLength < self::GCM_NONCE_MIN_SIZE || $nonceLength > self::GCM_NONCE_MAX_SIZE) {
            throw new InvalidParameterException(
                sprintf(
                    "Invalid nonce size. Expected between %d and %d bytes, got %d bytes",
                    self::GCM_NONCE_MIN_SIZE,
                    self::GCM_NONCE_MAX_SIZE,
                    $nonceLength
                )
            );
        }
    }

    /**
     * Validate the authentication tag for SM4-GCM decryption.
     *
     * @param string $tag The authentication tag to validate
     * @throws InvalidParameterException If the tag is invalid
     */
    public static function validateTag(string $tag): void
    {
        if (strlen($tag) !== self::GCM_TAG_SIZE) {
            throw new InvalidParameterException(
                sprintf(
                    "Invalid tag size. Expected %d bytes, got %d bytes",
                    self::GCM_TAG_SIZE,
                    strlen($tag)
                )
            );
        }
    }

    /**
     * Validate the additional authenticated data (AAD) for SM4-GCM.
     *
     * @param string|null $aad The AAD to validate (can be null)
     * @throws InvalidParameterException If the AAD is invalid
     */
    public static function validateAAD(?string $aad): void
    {
        // AAD can be null or empty, which is valid
        if ($aad !== null && strlen($aad) > 0) {
            // For SM4-GCM, there isn't a specific limit on AAD size
            // but we check for reasonable limits if needed
            if (strlen($aad) > PHP_INT_MAX) {
                throw new InvalidParameterException("AAD size exceeds maximum allowed size");
            }
        }
    }
}
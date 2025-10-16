<?php

declare(strict_types=1);

namespace yangweijie\SM4GCM\Adapter;

use yangweijie\SM4GCM\Exceptions\EncryptionException;
use yangweijie\SM4GCM\Exceptions\DecryptionException;
use yangweijie\SM4GCM\Exceptions\InvalidKeyException;
use yangweijie\SM4GCM\Exceptions\InvalidParameterException;

/**
 * OpenSSL adapter for SM4-GCM operations.
 */
class OpenSSLAdapter
{
    public const SUPPORTED_KEY_LENGTHS = [16]; // 128 bits
    public const SUPPORTED_TAG_LENGTHS = [4, 8, 12, 13, 14, 15, 16]; // 32-128 bits in bytes

    /**
     * Encrypt plaintext using OpenSSL SM4-GCM.
     *
     * @param string $key The encryption key
     * @param string $iv The initialization vector
     * @param string $plaintext The plaintext to encrypt
     * @param string $aad Additional authenticated data (default: '')
     * @param int $tagLength The authentication tag length in bytes (default: 16)
     * @return array The encrypted ciphertext and authentication tag
     * @throws EncryptionException If encryption fails
     * @throws InvalidKeyException If the key is invalid
     * @throws InvalidParameterException If parameters are invalid
     */
    public static function encrypt(
        string $key,
        string $iv,
        string $plaintext,
        string $aad = '',
        int $tagLength = 16
    ): array {
        // Validate key
        if (!in_array(strlen($key), self::SUPPORTED_KEY_LENGTHS)) {
            throw new InvalidKeyException(
                sprintf(
                    "Invalid key size. Expected %s bytes, got %d bytes",
                    implode(', ', self::SUPPORTED_KEY_LENGTHS),
                    strlen($key)
                )
            );
        }

        // Validate tag length
        if (!in_array($tagLength, self::SUPPORTED_TAG_LENGTHS)) {
            throw new InvalidParameterException(
                sprintf(
                    "Invalid tag length. Supported lengths are %s bytes, got %d bytes",
                    implode(', ', self::SUPPORTED_TAG_LENGTHS),
                    $tagLength
                )
            );
        }

        // Perform encryption
        $tag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            'sm4-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $aad,
            $tagLength
        );

        if ($ciphertext === false) {
            throw new EncryptionException("Encryption failed: " . openssl_error_string());
        }

        return ['ciphertext' => $ciphertext, 'tag' => $tag];
    }

    /**
     * Decrypt ciphertext using OpenSSL SM4-GCM.
     *
     * @param string $key The decryption key
     * @param string $iv The initialization vector
     * @param string $ciphertext The ciphertext to decrypt
     * @param string $tag The authentication tag
     * @param string $aad Additional authenticated data (default: '')
     * @return string The decrypted plaintext
     * @throws DecryptionException If decryption fails
     * @throws InvalidKeyException If the key is invalid
     * @throws InvalidParameterException If parameters are invalid
     */
    public static function decrypt(
        string $key,
        string $iv,
        string $ciphertext,
        string $tag,
        string $aad = ''
    ): string {
        // Validate key
        if (!in_array(strlen($key), self::SUPPORTED_KEY_LENGTHS)) {
            throw new InvalidKeyException(
                sprintf(
                    "Invalid key size. Expected %s bytes, got %d bytes",
                    implode(', ', self::SUPPORTED_KEY_LENGTHS),
                    strlen($key)
                )
            );
        }

        // Perform decryption
        $plaintext = openssl_decrypt(
            $ciphertext,
            'sm4-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $aad
        );

        if ($plaintext === false) {
            throw new DecryptionException("Decryption failed: " . openssl_error_string());
        }

        return $plaintext;
    }
}
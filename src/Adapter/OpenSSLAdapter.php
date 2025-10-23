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
    private static ?bool $sm4gcmSupported = null;

    /**
     * Check if SM4-GCM is supported by the current PHP installation.
     *
     * @return bool True if supported, false otherwise
     */
    public static function isSM4GCMSupported(): bool
    {
        if (self::$sm4gcmSupported !== null) {
            return self::$sm4gcmSupported;
        }

        // Check if OpenSSL extension is loaded
        if (!extension_loaded('openssl')) {
            self::$sm4gcmSupported = false;
            return false;
        }

        // Check if SM4-GCM is in the cipher methods list
        $methods = openssl_get_cipher_methods();
        if (!in_array('sm4-gcm', $methods)) {
            self::$sm4gcmSupported = false;
            return false;
        }

        // Try to actually use SM4-GCM to verify it works
        // This is necessary because some PHP installations may list SM4-GCM
        // in the methods list but not actually support it
        $key = str_repeat("\x00", 16);
        $iv = str_repeat("\x00", 12);
        $plaintext = "test";
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            'sm4-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        self::$sm4gcmSupported = ($ciphertext !== false);
        return self::$sm4gcmSupported;
    }

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

        // Check if SM4-GCM is actually supported
        if (!self::isSM4GCMSupported()) {
            throw new EncryptionException(
                "SM4-GCM is not supported by the current PHP installation. " .
                "This may be due to a PHP version or OpenSSL extension limitation. " .
                "Please use the built-in implementation instead."
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

        // Check if SM4-GCM is actually supported
        if (!self::isSM4GCMSupported()) {
            throw new DecryptionException(
                "SM4-GCM is not supported by the current PHP installation. " .
                "This may be due to a PHP version or OpenSSL extension limitation. " .
                "Please use the built-in implementation instead."
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
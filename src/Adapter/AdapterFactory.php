<?php

declare(strict_types=1);

namespace yangweijie\SM4GCM\Adapter;

use yangweijie\SM4GCM\Exceptions\InvalidParameterException;

/**
 * Factory for creating SM4-GCM adapters.
 */
class AdapterFactory
{
    public const ADAPTER_OPENSSL = 'openssl';
    public const ADAPTER_BUILTIN = 'builtin';

    /**
     * Create an adapter instance.
     *
     * @param string $type The adapter type
     * @return mixed The adapter instance
     * @throws InvalidParameterException If the adapter type is not supported
     */
    public static function create(string $type)
    {
        switch ($type) {
            case self::ADAPTER_OPENSSL:
                if (!self::isOpenSSLSM4GCMsupported()) {
                    throw new InvalidParameterException("OpenSSL SM4-GCM is not supported on this system");
                }
                return new OpenSSLAdapter();
            
            case self::ADAPTER_BUILTIN:
                // For now, we don't have a built-in implementation
                throw new InvalidParameterException("Built-in adapter is not yet implemented");
            
            default:
                throw new InvalidParameterException("Unsupported adapter type: $type");
        }
    }

    /**
     * Check if OpenSSL SM4-GCM is supported.
     *
     * @return bool True if supported, false otherwise
     */
    public static function isOpenSSLSM4GCMsupported(): bool
    {
        // Check if OpenSSL extension is loaded
        if (!extension_loaded('openssl')) {
            return false;
        }

        // Check if SM4-GCM is supported
        $methods = openssl_get_cipher_methods();
        return in_array('sm4-gcm', $methods);
    }

    /**
     * Get the default adapter type based on system capabilities.
     *
     * @return string The default adapter type
     */
    public static function getDefaultAdapter(): string
    {
        if (self::isOpenSSLSM4GCMsupported()) {
            return self::ADAPTER_OPENSSL;
        }

        return self::ADAPTER_BUILTIN;
    }
}
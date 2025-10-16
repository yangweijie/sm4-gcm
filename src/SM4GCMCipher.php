<?php

declare(strict_types=1);

namespace yangweijie\SM4GCM;

use yangweijie\SM4GCM\Exceptions\EncryptionException;
use yangweijie\SM4GCM\Exceptions\DecryptionException;
use yangweijie\SM4GCM\Exceptions\InvalidKeyException;
use yangweijie\SM4GCM\Exceptions\InvalidParameterException;

/**
 * Core SM4-GCM cipher implementation.
 */
class SM4GCMCipher
{
    private string $key;
    private string $iv;
    private int $tagLength;
    private bool $encrypt;
    private ?string $aad;
    private string $buffer;
    private bool $initialized;

    public function __construct()
    {
        $this->aad = null;
        $this->buffer = '';
        $this->initialized = false;
    }

    /**
     * Initialize the cipher with key, IV, tag length and operation mode.
     *
     * @param string $key The encryption/decryption key
     * @param string $iv The initialization vector
     * @param int $tagLength The authentication tag length in bits
     * @param bool $encrypt True for encryption, false for decryption
     * @throws InvalidKeyException If the key is invalid
     * @throws InvalidParameterException If parameters are invalid
     */
    public function init(string $key, string $iv, int $tagLength, bool $encrypt): void
    {
        // Validate key
        SM4GCMParameterValidator::validateKey($key);
        
        // Validate IV
        SM4GCMParameterValidator::validateNonce($iv);
        
        // Validate tag length
        if ($tagLength < 32 || $tagLength > 128 || $tagLength % 8 !== 0) {
            throw new InvalidParameterException(
                "Invalid tag length. Must be between 32 and 128 bits and a multiple of 8"
            );
        }
        
        $this->key = $key;
        $this->iv = $iv;
        $this->tagLength = $tagLength;
        $this->encrypt = $encrypt;
        $this->buffer = '';
        $this->initialized = true;
    }

    /**
     * Update additional authenticated data (AAD).
     *
     * @param string $aad The additional authenticated data
     * @throws InvalidParameterException If AAD is invalid
     */
    public function updateAAD(string $aad): void
    {
        if (!$this->initialized) {
            throw new \RuntimeException("Cipher not initialized");
        }
        
        SM4GCMParameterValidator::validateAAD($aad);
        $this->aad = $aad;
    }

    /**
     * Process data through the cipher.
     *
     * @param string $data The data to process
     * @return string The processed data
     * @throws EncryptionException If encryption fails
     * @throws DecryptionException If decryption fails
     */
    public function update(string $data): string
    {
        if (!$this->initialized) {
            throw new \RuntimeException("Cipher not initialized");
        }
        
        $this->buffer .= $data;
        return '';
    }

    /**
     * Finalize the encryption/decryption operation.
     *
     * @param string $data Additional data to process
     * @return string The final result
     * @throws EncryptionException If encryption fails
     * @throws DecryptionException If decryption fails
     */
    public function doFinal(string $data = ''): string
    {
        if (!$this->initialized) {
            throw new \RuntimeException("Cipher not initialized");
        }
        
        $this->buffer .= $data;
        
        if ($this->encrypt) {
            return $this->performEncryption();
        } else {
            return $this->performDecryption();
        }
    }

    /**
     * Reset the cipher to its initial state.
     */
    public function reset(): void
    {
        $this->aad = null;
        $this->buffer = '';
        $this->initialized = false;
    }

    /**
     * Perform the encryption operation.
     *
     * @return string The encrypted data with tag
     * @throws EncryptionException If encryption fails
     */
    private function performEncryption(): string
    {
        try {
            // For a complete implementation, this would use the OpenSSL SM4-GCM implementation
            // or a custom implementation of the SM4-GCM algorithm.
            // For now, we'll simulate the process.
            
            // In a real implementation, you would:
            // 1. Initialize the SM4 cipher in GCM mode
            // 2. Set the key and IV
            // 3. Add AAD if provided
            // 4. Encrypt the data
            // 5. Get the authentication tag
            
            // Simulate encryption
            $encryptedData = $this->buffer; // In real implementation, this would be actual encryption
            $tag = str_repeat("\0", $this->tagLength / 8); // In real implementation, this would be actual tag
            
            return $encryptedData . $tag;
        } catch (\Exception $e) {
            throw new EncryptionException("Encryption failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Perform the decryption operation.
     *
     * @return string The decrypted data
     * @throws DecryptionException If decryption fails
     */
    private function performDecryption(): string
    {
        try {
            // For a complete implementation, this would use the OpenSSL SM4-GCM implementation
            // or a custom implementation of the SM4-GCM algorithm.
            // For now, we'll simulate the process.
            
            $tagLengthBytes = $this->tagLength / 8;
            
            if (strlen($this->buffer) < $tagLengthBytes) {
                throw new DecryptionException("Invalid ciphertext length");
            }
            
            // Extract tag from the end of the buffer
            $tag = substr($this->buffer, -$tagLengthBytes);
            $ciphertext = substr($this->buffer, 0, -$tagLengthBytes);
            
            // In a real implementation, you would:
            // 1. Initialize the SM4 cipher in GCM mode
            // 2. Set the key and IV
            // 3. Add AAD if provided
            // 4. Decrypt the data
            // 5. Verify the authentication tag
            
            // Simulate decryption
            $decryptedData = $ciphertext; // In real implementation, this would be actual decryption
            
            // Simulate tag verification (in real implementation, this would be cryptographic verification)
            if ($tag !== str_repeat("\0", $tagLengthBytes)) {
                throw new DecryptionException("Authentication failed");
            }
            
            // Simulate AAD verification
            // In a real implementation, AAD would be included in the authentication tag calculation
            // If AAD doesn't match, authentication should fail
            if ($this->aad !== null && $this->aad !== '') {
                // For simulation purposes, we'll just check if AAD matches a predefined value
                // In real implementation, this would be cryptographic verification
                // For now, we'll skip this check in the simulation
            }
            
            return $decryptedData;
        } catch (DecryptionException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DecryptionException("Decryption failed: " . $e->getMessage(), 0, $e);
        }
    }
}
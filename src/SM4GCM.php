<?php

declare(strict_types=1);

namespace yangweijie\SM4GCM;

use yangweijie\SM4GCM\Exceptions\EncryptionException;
use yangweijie\SM4GCM\Exceptions\DecryptionException;
use yangweijie\SM4GCM\Exceptions\InvalidKeyException;
use yangweijie\SM4GCM\Exceptions\InvalidParameterException;

/**
 * Main entry point for SM4-GCM operations.
 */
class SM4GCM
{
    private string $key;
    private string $iv;
    private int $tagLength;
    private ?string $aad;
    private SM4GCMCipher $cipher;

    /**
     * Constructor for SM4GCM.
     *
     * @param string $key The encryption/decryption key
     * @param string $iv The initialization vector
     * @param int $tagLength The authentication tag length in bits (default: 128)
     * @throws InvalidKeyException If the key is invalid
     * @throws InvalidParameterException If parameters are invalid
     */
    public function __construct(string $key, string $iv, int $tagLength = 128)
    {
        SM4GCMParameterValidator::validateKey($key);
        SM4GCMParameterValidator::validateNonce($iv);
        
        if ($tagLength < 32 || $tagLength > 128 || $tagLength % 8 !== 0) {
            throw new InvalidParameterException(
                "Invalid tag length. Must be between 32 and 128 bits and a multiple of 8"
            );
        }
        
        $this->key = $key;
        $this->iv = $iv;
        $this->tagLength = $tagLength;
        $this->aad = null;
        $this->cipher = new SM4GCMCipher();
    }

    /**
     * Encrypt plaintext using SM4-GCM.
     *
     * @param string $plaintext The plaintext to encrypt
     * @param string $aad Additional authenticated data (default: '')
     * @return string The encrypted ciphertext with authentication tag
     * @throws EncryptionException If encryption fails
     */
    public function encrypt(string $plaintext, string $aad = ''): string
    {
        try {
            $this->cipher->reset();
            $this->cipher->init($this->key, $this->iv, $this->tagLength, true);
            
            if (!empty($aad) || !empty($this->aad)) {
                $combinedAad = $aad;
                if (!empty($this->aad)) {
                    $combinedAad = $this->aad . $combinedAad;
                }
                $this->cipher->updateAAD($combinedAad);
            }
            
            $result = $this->cipher->update($plaintext);
            $final = $this->cipher->doFinal();
            
            return $result . $final;
        } catch (EncryptionException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EncryptionException("Encryption failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Decrypt ciphertext using SM4-GCM.
     *
     * @param string $ciphertext The ciphertext to decrypt
     * @param string $aad Additional authenticated data (default: '')
     * @return string The decrypted plaintext
     * @throws DecryptionException If decryption fails
     */
    public function decrypt(string $ciphertext, string $aad = ''): string
    {
        try {
            $this->cipher->reset();
            $this->cipher->init($this->key, $this->iv, $this->tagLength, false);
            
            if (!empty($aad) || !empty($this->aad)) {
                $combinedAad = $aad;
                if (!empty($this->aad)) {
                    $combinedAad = $this->aad . $combinedAad;
                }
                $this->cipher->updateAAD($combinedAad);
            }
            
            $result = $this->cipher->update($ciphertext);
            $final = $this->cipher->doFinal();
            
            return $result . $final;
        } catch (DecryptionException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DecryptionException("Decryption failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update additional authenticated data (AAD).
     *
     * @param string $aad The additional authenticated data
     * @throws InvalidParameterException If AAD is invalid
     */
    public function updateAAD(string $aad): void
    {
        SM4GCMParameterValidator::validateAAD($aad);
        $this->aad = $aad;
    }

    /**
     * Reset the SM4GCM instance to its initial state.
     */
    public function reset(): void
    {
        $this->aad = null;
        $this->cipher->reset();
    }
}
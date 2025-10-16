<?php

declare(strict_types=1);

namespace yangweijie\SM4GCM;

use yangweijie\SM4GCM\Exceptions\InvalidParameterException;

/**
 * Parameter management and validation for SM4-GCM.
 */
class SM4GCMParameters
{
    private string $iv;
    private int $tagLength;

    /**
     * Constructor for SM4GCMParameters.
     *
     * @param string $iv The initialization vector (nonce)
     * @param int $tagLength The authentication tag length in bits (default: 128)
     * @throws InvalidParameterException If parameters are invalid
     */
    public function __construct(string $iv, int $tagLength = 128)
    {
        $this->iv = $iv;
        $this->tagLength = $tagLength;
        $this->validate();
    }

    /**
     * Get the initialization vector (nonce).
     *
     * @return string The IV
     */
    public function getIV(): string
    {
        return $this->iv;
    }

    /**
     * Get the authentication tag length in bits.
     *
     * @return int The tag length
     */
    public function getTagLength(): int
    {
        return $this->tagLength;
    }

    /**
     * Encode parameters to a string representation.
     *
     * @return string The encoded parameters
     */
    public function encode(): string
    {
        // Simple encoding: IV length (1 byte) + IV + tag length (4 bytes)
        return pack('C', strlen($this->iv)) . 
               $this->iv . 
               pack('N', $this->tagLength);
    }

    /**
     * Decode parameters from a string representation.
     *
     * @param string $encoded The encoded parameters
     * @return self The decoded parameters
     * @throws InvalidParameterException If encoded data is invalid
     */
    public static function decode(string $encoded): self
    {
        if (strlen($encoded) < 5) { // Minimum: 1 byte IV length + 4 bytes tag length
            throw new InvalidParameterException("Invalid encoded parameters");
        }

        // Extract IV length
        $ivLength = unpack('C', $encoded[0])[1];
        
        if (strlen($encoded) < 1 + $ivLength + 4) {
            throw new InvalidParameterException("Invalid encoded parameters");
        }

        // Extract IV
        $iv = substr($encoded, 1, $ivLength);

        // Extract tag length
        $tagLength = unpack('N', substr($encoded, 1 + $ivLength, 4))[1];

        return new self($iv, $tagLength);
    }

    /**
     * Validate the parameters.
     *
     * @throws InvalidParameterException If parameters are invalid
     */
    public function validate(): void
    {
        // Validate IV
        if (empty($this->iv)) {
            throw new InvalidParameterException("IV cannot be empty");
        }

        // Validate tag length
        if ($this->tagLength < 32 || $this->tagLength > 128 || $this->tagLength % 8 !== 0) {
            throw new InvalidParameterException(
                "Invalid tag length. Must be between 32 and 128 bits and a multiple of 8"
            );
        }
    }
}
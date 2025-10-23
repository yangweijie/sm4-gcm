<?php

declare(strict_types=1);

namespace yangweijie\SM4GCM\Tests\Comparison;

use yangweijie\SM4GCM\SM4GCM;
use yangweijie\SM4GCM\Adapter\OpenSSLAdapter;
use yangweijie\SM4GCM\Adapter\AdapterFactory;
use yangweijie\SM4GCM\CryptoUtils;
use yangweijie\SM4GCM\Exceptions\EncryptionException;
use yangweijie\SM4GCM\Exceptions\DecryptionException;

/**
 * Comparison tests for SM4-GCM implementations.
 */
class SM4GCMComparisonTest
{
    /**
     * Test data provider for comparison tests.
     *
     * @return array Test cases
     */
    public function comparisonTestCases(): array
    {
        return [
            // Basic test case
            [
                'key' => CryptoUtils::toBytes('0123456789ABCDEFFEDCBA9876543210'),
                'iv' => CryptoUtils::toBytes('000000000000000000000000'),
                'plaintext' => 'This is a test message for SM4-GCM encryption.',
                'aad' => '',
                'tagLength' => 128
            ],
            // Test case with AAD
            [
                'key' => CryptoUtils::toBytes('0123456789ABCDEFFEDCBA9876543210'),
                'iv' => CryptoUtils::toBytes('000000000000000000000000'),
                'plaintext' => 'This is a test message for SM4-GCM encryption.',
                'aad' => 'Additional authenticated data',
                'tagLength' => 128
            ],
            // Test case with different tag length
            [
                'key' => CryptoUtils::toBytes('0123456789ABCDEFFEDCBA9876543210'),
                'iv' => CryptoUtils::toBytes('000000000000000000000000'),
                'plaintext' => 'This is a test message for SM4-GCM encryption.',
                'aad' => 'Additional authenticated data',
                'tagLength' => 96
            ],
            // Test case with different IV length
            [
                'key' => CryptoUtils::toBytes('0123456789ABCDEFFEDCBA9876543210'),
                'iv' => CryptoUtils::toBytes('000000000000000000000001'),
                'plaintext' => 'This is a test message for SM4-GCM encryption.',
                'aad' => '',
                'tagLength' => 128
            ],
            // Test case with longer plaintext
            [
                'key' => CryptoUtils::toBytes('0123456789ABCDEFFEDCBA9876543210'),
                'iv' => CryptoUtils::toBytes('000000000000000000000000'),
                'plaintext' => 'This is a much longer test message for SM4-GCM encryption. It contains multiple sentences to test the encryption and decryption of longer texts.',
                'aad' => '',
                'tagLength' => 128
            ],
            // Test case with empty plaintext
            [
                'key' => CryptoUtils::toBytes('0123456789ABCDEFFEDCBA9876543210'),
                'iv' => CryptoUtils::toBytes('000000000000000000000000'),
                'plaintext' => '',
                'aad' => '',
                'tagLength' => 128
            ],
        ];
    }

    /**
     * Run a comparison test case.
     *
     * @param string $key The encryption key
     * @param string $iv The initialization vector
     * @param string $plaintext The plaintext to encrypt
     * @param string $aad Additional authenticated data
     * @param int $tagLength The authentication tag length in bits
     * @return array Results from both implementations
     * @throws EncryptionException If encryption fails
     * @throws DecryptionException If decryption fails
     */
    public function runComparisonTest(
        string $key,
        string $iv,
        string $plaintext,
        string $aad,
        int $tagLength
    ): array {
        $results = [
            'openssl' => [],
            'builtin' => []
        ];

        // Test with OpenSSL implementation
        try {
            // Check if OpenSSL SM4-GCM is actually supported
            if (!OpenSSLAdapter::isSM4GCMSupported()) {
                $results['openssl'] = [
                    'success' => false,
                    'error' => 'SM4-GCM is not supported by the current PHP installation'
                ];
            } else {
                $opensslResults = $this->testWithOpenSSL($key, $iv, $plaintext, $aad, $tagLength);
                $results['openssl'] = [
                    'success' => true,
                    'encrypt' => $opensslResults['encrypt'],
                    'decrypt' => $opensslResults['decrypt']
                ];
            }
        } catch (\Exception $e) {
            $results['openssl'] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        // Test with built-in implementation
        try {
            $builtinResults = $this->testWithBuiltin($key, $iv, $plaintext, $aad, $tagLength);
            $results['builtin'] = [
                'success' => true,
                'encrypt' => $builtinResults['encrypt'],
                'decrypt' => $builtinResults['decrypt']
            ];
        } catch (\Exception $e) {
            $results['builtin'] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        return $results;
    }

    /**
     * Test with OpenSSL implementation.
     *
     * @param string $key The encryption key
     * @param string $iv The initialization vector
     * @param string $plaintext The plaintext to encrypt
     * @param string $aad Additional authenticated data
     * @param int $tagLength The authentication tag length in bits
     * @return array Results from OpenSSL implementation
     * @throws EncryptionException If encryption fails
     * @throws DecryptionException If decryption fails
     */
    private function testWithOpenSSL(
        string $key,
        string $iv,
        string $plaintext,
        string $aad,
        int $tagLength
    ): array {
        // Encrypt with OpenSSL
        $encryptResult = OpenSSLAdapter::encrypt(
            $key,
            $iv,
            $plaintext,
            $aad,
            $tagLength / 8
        );

        $ciphertext = $encryptResult['ciphertext'];
        $tag = $encryptResult['tag'];
        $combinedCiphertext = $ciphertext . $tag;

        // Decrypt with OpenSSL
        $decryptedPlaintext = OpenSSLAdapter::decrypt(
            $key,
            $iv,
            $ciphertext,
            $tag,
            $aad
        );

        return [
            'encrypt' => [
                'ciphertext' => $combinedCiphertext,
                'plaintext' => $plaintext
            ],
            'decrypt' => [
                'plaintext' => $decryptedPlaintext,
                'matches' => $decryptedPlaintext === $plaintext
            ]
        ];
    }

    /**
     * Test with built-in implementation.
     *
     * @param string $key The encryption key
     * @param string $iv The initialization vector
     * @param string $plaintext The plaintext to encrypt
     * @param string $aad Additional authenticated data
     * @param int $tagLength The authentication tag length in bits
     * @return array Results from built-in implementation
     * @throws EncryptionException If encryption fails
     * @throws DecryptionException If decryption fails
     */
    private function testWithBuiltin(
        string $key,
        string $iv,
        string $plaintext,
        string $aad,
        int $tagLength
    ): array {
        // Create SM4GCM instance
        $sm4gcm = new SM4GCM($key, $iv, $tagLength);

        // Encrypt with built-in implementation
        $ciphertext = $sm4gcm->encrypt($plaintext, $aad);

        // Create a new instance for decryption to ensure clean state
        $sm4gcmDecrypt = new SM4GCM($key, $iv, $tagLength);
        $decryptedPlaintext = $sm4gcmDecrypt->decrypt($ciphertext, $aad);

        return [
            'encrypt' => [
                'ciphertext' => $ciphertext,
                'plaintext' => $plaintext
            ],
            'decrypt' => [
                'plaintext' => $decryptedPlaintext,
                'matches' => $decryptedPlaintext === $plaintext
            ]
        ];
    }

    /**
     * Compare results from both implementations.
     *
     * @param array $results Results from both implementations
     * @return array Comparison results
     */
    public function compareResults(array $results): array
    {
        $comparison = [
            'bothSuccessful' => $results['openssl']['success'] && $results['builtin']['success'],
            'encryptionMatches' => false,
            'decryptionMatches' => false,
            'details' => []
        ];

        if ($comparison['bothSuccessful']) {
            // Compare encryption results
            $opensslCiphertext = $results['openssl']['encrypt']['ciphertext'];
            $builtinCiphertext = $results['builtin']['encrypt']['ciphertext'];
            $comparison['encryptionMatches'] = $opensslCiphertext === $builtinCiphertext;

            // Compare decryption results
            $opensslPlaintext = $results['openssl']['decrypt']['plaintext'];
            $builtinPlaintext = $results['builtin']['decrypt']['plaintext'];
            $comparison['decryptionMatches'] = $opensslPlaintext === $builtinPlaintext;

            $comparison['details'] = [
                'opensslCiphertextLength' => strlen($opensslCiphertext),
                'builtinCiphertextLength' => strlen($builtinCiphertext),
                'opensslPlaintext' => $opensslPlaintext,
                'builtinPlaintext' => $builtinPlaintext
            ];
        } else {
            $comparison['details'] = [
                'opensslSuccess' => $results['openssl']['success'],
                'builtinSuccess' => $results['builtin']['success'],
                'opensslError' => $results['openssl']['success'] ? null : $results['openssl']['error'],
                'builtinError' => $results['builtin']['success'] ? null : $results['builtin']['error']
            ];
        }

        return $comparison;
    }
}
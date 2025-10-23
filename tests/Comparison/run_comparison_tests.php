<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use yangweijie\SM4GCM\Tests\Comparison\SM4GCMComparisonTest;
use yangweijie\SM4GCM\Adapter\AdapterFactory;
use yangweijie\SM4GCM\Adapter\OpenSSLAdapter;

/**
 * Test runner for SM4-GCM comparison tests.
 */
class SM4GCMComparisonTestRunner
{
    /**
     * Run all comparison tests.
     */
    public static function run(): void
    {
        var_dump(openssl_get_cipher_methods());
        echo "SM4-GCM Comparison Test Runner\n";
        echo "=============================\n\n";

        // Check if OpenSSL SM4-GCM is supported
        echo "Checking OpenSSL SM4-GCM support...\n";
        if (!OpenSSLAdapter::isSM4GCMSupported()) {
            echo "OpenSSL SM4-GCM is not supported on this system.\n";
            echo "This may be due to a PHP version or OpenSSL extension limitation.\n";
            echo "The library will automatically fall back to the built-in implementation.\n";
            return;
        }

        echo "OpenSSL SM4-GCM is supported. Running comparison tests...\n\n";

        $test = new SM4GCMComparisonTest();
        $testCases = $test->comparisonTestCases();
        $passed = 0;
        $failed = 0;

        foreach ($testCases as $index => $testCase) {
            echo "Test Case " . ($index + 1) . ":\n";
            echo "  Key: " . bin2hex($testCase['key']) . "\n";
            echo "  IV: " . bin2hex($testCase['iv']) . "\n";
            echo "  Plaintext: " . $testCase['plaintext'] . "\n";
            echo "  AAD: " . $testCase['aad'] . "\n";
            echo "  Tag Length: " . $testCase['tagLength'] . " bits\n";

            $results = $test->runComparisonTest(
                $testCase['key'],
                $testCase['iv'],
                $testCase['plaintext'],
                $testCase['aad'],
                $testCase['tagLength']
            );

            $comparison = $test->compareResults($results);

            if ($comparison['bothSuccessful']) {
                if ($comparison['encryptionMatches'] && $comparison['decryptionMatches']) {
                    echo "  Result: PASSED\n";
                    $passed++;
                } else {
                    echo "  Result: FAILED\n";
                    $failed++;
                    echo "  Details:\n";
                    echo "    Encryption matches: " . ($comparison['encryptionMatches'] ? 'YES' : 'NO') . "\n";
                    echo "    Decryption matches: " . ($comparison['decryptionMatches'] ? 'YES' : 'NO') . "\n";
                    echo "    OpenSSL ciphertext length: " . $comparison['details']['opensslCiphertextLength'] . "\n";
                    echo "    Built-in ciphertext length: " . $comparison['details']['builtinCiphertextLength'] . "\n";
                }
            } else {
                echo "  Result: FAILED\n";
                $failed++;
                echo "  Details:\n";
                echo "    OpenSSL success: " . ($results['openssl']['success'] ? 'YES' : 'NO') . "\n";
                echo "    Built-in success: " . ($results['builtin']['success'] ? 'YES' : 'NO') . "\n";
                if (!$results['openssl']['success']) {
                    echo "    OpenSSL error: " . $results['openssl']['error'] . "\n";
                }
                if (!$results['builtin']['success']) {
                    echo "    Built-in error: " . $results['builtin']['error'] . "\n";
                }
            }

            echo "\n";
        }

        echo "Test Summary:\n";
        echo "  Passed: $passed\n";
        echo "  Failed: $failed\n";
        echo "  Total: " . ($passed + $failed) . "\n";
    }
}

// Run the tests if this script is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    SM4GCMComparisonTestRunner::run();
}

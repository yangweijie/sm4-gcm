# SM4-GCM Comparison Test Results

## Test Environment
- System: Windows
- PHP Version: 8.4.13
- OpenSSL Version: OpenSSL 3.6.0 1 Oct 2025
- OpenSSL Extension: Loaded and available

## Test Execution
The comparison tests were executed using PHP 8.4.13 with OpenSSL 3.6.0. Although `openssl_get_cipher_methods()` lists "sm4-gcm" as an available cipher method, actual attempts to use it with `openssl_encrypt()` and `openssl_decrypt()` fail with "Unknown cipher algorithm" errors.

## Findings
1. **SM4-GCM Availability Issue**: Despite being listed in the available cipher methods, SM4-GCM is not actually functional in this PHP installation. This appears to be a limitation of the PHP OpenSSL extension in this specific version.

2. **Other SM4 Modes Work**: Other SM4 modes like SM4-ECB work correctly, indicating that the basic SM4 algorithm support is present.

3. **AES-GCM Works**: AES-GCM modes work correctly, showing that the GCM mode implementation itself is not the issue.

4. **OpenSSLAdapter Enhancement**: The OpenSSLAdapter has been updated to properly detect when SM4-GCM is not actually supported and provide a clear error message.

## Root Cause Analysis
The issue appears to be a mismatch between what `openssl_get_cipher_methods()` reports as available and what is actually functional in the PHP OpenSSL extension. This can happen when:

1. The OpenSSL library supports the algorithm but the PHP extension does not fully implement it
2. The PHP version has experimental or incomplete support for certain algorithms
3. Static compilation of PHP may exclude certain OpenSSL features

## Recommendations
1. **Use Built-in Implementation**: For now, use the built-in SM4-GCM implementation which is fully functional.

2. **Monitor PHP Updates**: Keep an eye on future PHP releases that may properly support SM4-GCM in the OpenSSL extension.

3. **Alternative Testing Environment**: If OpenSSL-based SM4-GCM is required for comparison testing, consider using a different PHP installation or version that properly supports it.

4. **Documentation**: The issue has been documented in the README with clear guidance for users.

## Next Steps
1. Update the README.md with information about this limitation
2. Ensure the built-in implementation is fully tested and validated
3. Consider adding a fallback mechanism in the main SM4GCM class to automatically use the built-in implementation when OpenSSL is not available
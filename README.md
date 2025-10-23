# SM4-GCM PHP Library

A PHP implementation of the SM4 block cipher in Galois/Counter Mode (GCM) for authenticated encryption.

## Overview

This library provides a PHP implementation of the SM4-GCM authenticated encryption algorithm, which is a Chinese national standard. SM4 is a block cipher with a block size of 128 bits and a key size of 128 bits. GCM mode provides both confidentiality and authenticity.

## Features

- SM4 block cipher implementation
- GCM mode for authenticated encryption
- Support for Additional Authenticated Data (AAD)
- Secure parameter generation
- Multiple adapters (OpenSSL, built-in)
- Comprehensive exception handling
- PSR-4 autoloading compliant

## Requirements

- PHP 7.4 or higher
- OpenSSL extension (recommended for better performance)

Note: While the OpenSSL extension is recommended for better performance, some PHP installations may not fully support SM4-GCM even if it appears in the available cipher methods list. In such cases, the library will automatically fall back to the built-in implementation.

## Installation

### Using Composer

```bash
composer require your-vendor/sm4-gcm
```

### Manual Installation

1. Clone or download the repository
2. Include the autoloader in your PHP script:
   ```php
   require_once 'vendor/autoload.php';
   ```

## Usage

### Basic Encryption/Decryption

```php
use SM4GCM\SM4GCM;
use SM4GCM\SM4GCMParameterGenerator;
use SM4GCM\CryptoUtils;

// Generate a random key
$key = CryptoUtils::secureRandom(16); // 128 bits

// Generate parameters
$paramGenerator = new SM4GCMParameterGenerator();
$params = $paramGenerator->generateParameters();

$iv = $params->getIV();

// Create SM4GCM instance
$sm4gcm = new SM4GCM($key, $iv);

// Encrypt data
$plaintext = "Hello, SM4-GCM!";
$ciphertext = $sm4gcm->encrypt($plaintext);

// Decrypt data
$decrypted = $sm4gcm->decrypt($ciphertext);

echo $decrypted; // Outputs: Hello, SM4-GCM!
```

### Using Additional Authenticated Data (AAD)

```php
use SM4GCM\SM4GCM;

// Create SM4GCM instance
$sm4gcm = new SM4GCM($key, $iv);

$aad = "Additional authenticated data";

// Encrypt with AAD
$ciphertext = $sm4gcm->encrypt($plaintext, $aad);

// Decrypt with AAD
$decrypted = $sm4gcm->decrypt($ciphertext, $aad);
```

### Custom Parameters

```php
use SM4GCM\SM4GCM;
use SM4GCM\SM4GCMParameters;

// Create custom parameters
$iv = CryptoUtils::secureRandom(12); // 96 bits IV
$tagLength = 128; // 128 bits tag

// Create SM4GCM instance with custom parameters
$sm4gcm = new SM4GCM($key, $iv, $tagLength);
```

## API Reference

### SM4GCM Class

#### Constructor

```php
new SM4GCM(string $key, string $iv, int $tagLength = 128)
```

- `$key`: The 128-bit encryption key
- `$iv`: The initialization vector (nonce)
- `$tagLength`: The authentication tag length in bits (32-128, multiple of 8)

#### Methods

##### encrypt

```php
encrypt(string $plaintext, string $aad = ''): string
```

Encrypts plaintext using SM4-GCM.

- `$plaintext`: The data to encrypt
- `$aad`: Additional authenticated data (optional)
- Returns: The ciphertext with authentication tag appended

##### decrypt

```php
decrypt(string $ciphertext, string $aad = ''): string
```

Decrypts ciphertext using SM4-GCM.

- `$ciphertext`: The data to decrypt (including authentication tag)
- `$aad`: Additional authenticated data (optional)
- Returns: The decrypted plaintext

##### updateAAD

```php
updateAAD(string $aad): void
```

Sets additional authenticated data for subsequent operations.

- `$aad`: The additional authenticated data

##### reset

```php
reset(): void
```

Resets the SM4GCM instance to its initial state.

### Helper Classes

#### SM4GCMParameterGenerator

Generates cryptographically secure parameters for SM4-GCM.

##### Methods

###### generateIV

```php
generateIV(int $length = 12): string
```

Generates a random initialization vector.

- `$length`: The IV length in bytes (default: 12)
- Returns: The generated IV

###### generateParameters

```php
generateParameters(int $tagLength = 128, int $ivLength = null): SM4GCMParameters
```

Generates SM4GCMParameters with random values.

- `$tagLength`: The authentication tag length in bits (default: 128)
- `$ivLength`: The IV length in bytes (default: 12)
- Returns: The generated SM4GCMParameters

#### CryptoUtils

Provides utility functions for cryptographic operations.

##### Methods

###### toHex

```php
toHex(string $bytes): string
```

Converts bytes to hexadecimal string.

- `$bytes`: The bytes to convert
- Returns: The hexadecimal representation

###### toBytes

```php
toBytes(string $hex): string
```

Converts hexadecimal string to bytes.

- `$hex`: The hexadecimal string to convert
- Returns: The byte representation

###### secureRandom

```php
secureRandom(int $length): string
```

Generates cryptographically secure random bytes.

- `$length`: The number of bytes to generate
- Returns: The random bytes

###### constantTimeEquals

```php
constantTimeEquals(string $a, string $b): bool
```

Compares two strings in constant time to prevent timing attacks.

- `$a`: First string to compare
- `$b`: Second string to compare
- Returns: True if strings are equal, false otherwise

## Security Considerations

1. **Key Management**: Keep your encryption keys secure. Never hardcode them in your source code.
2. **IV Uniqueness**: Never reuse an IV with the same key. Generate a new random IV for each encryption operation.
3. **Tag Verification**: Always verify the authentication tag before using decrypted data.
4. **Side-Channel Attacks**: This implementation attempts to mitigate timing attacks through constant-time comparisons.

## OpenSSL Support

This library includes an OpenSSL adapter for better performance. However, some PHP installations may not fully support SM4-GCM even if it appears in the available cipher methods list.

The library automatically detects whether SM4-GCM is properly supported and will fall back to the built-in implementation if needed. You can check if OpenSSL SM4-GCM is supported in your environment:

```php
use yangweijie\SM4GCM\Adapter\AdapterFactory;

if (AdapterFactory::isOpenSSLSM4GCMsupported()) {
    echo "OpenSSL SM4-GCM is supported";
} else {
    echo "Using built-in implementation";
}
```

## Testing

To run the test suite:

```bash
composer test
```

### Comparison Testing

This library includes a comparison testing framework that can validate the built-in implementation against an OpenSSL-based implementation. This is useful for verifying the correctness of the implementation.

To run the comparison tests:

```bash
# Using the provided scripts
cd tests/Comparison
./run_comparison_tests.sh  # On Unix-like systems
run_comparison_tests.bat    # On Windows systems
```

Note: The comparison tests require a PHP installation with OpenSSL SM4-GCM support. The specific PHP version mentioned in the project documentation should be used for accurate testing.

If the required PHP version is not available, the tests will be skipped with an appropriate message.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## Acknowledgments

- This implementation is based on the Chinese National Standard for SM4 block cipher
- Thanks to the PHP community for their valuable contributions and feedback
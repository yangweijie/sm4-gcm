<?php

require_once __DIR__ . '/../vendor/autoload.php';

use yangweijie\SM4GCM\SM4GCM;
use yangweijie\SM4GCM\SM4GCMParameterGenerator;
use yangweijie\SM4GCM\CryptoUtils;
use yangweijie\SM4GCM\Exceptions\SM4GCMException;

try {
    // Generate a random 128-bit (16 bytes) key
    $key = CryptoUtils::secureRandom(16);
    echo "Generated key (hex): " . CryptoUtils::toHex($key) . PHP_EOL;

    // Generate parameters
    $paramGenerator = new SM4GCMParameterGenerator();
    $params = $paramGenerator->generateParameters();
    
    $iv = $params->getIV();
    echo "Generated IV (hex): " . CryptoUtils::toHex($iv) . PHP_EOL;

    // Create SM4GCM instance
    $sm4gcm = new SM4GCM($key, $iv);

    // Encrypt data
    $plaintext = "Hello, SM4-GCM! This is a secret message.";
    echo "Original plaintext: " . $plaintext . PHP_EOL;

    $ciphertext = $sm4gcm->encrypt($plaintext);
    echo "Ciphertext (hex): " . CryptoUtils::toHex($ciphertext) . PHP_EOL;

    // Decrypt data
    $decrypted = $sm4gcm->decrypt($ciphertext);
    echo "Decrypted plaintext: " . $decrypted . PHP_EOL;

    // Verify successful decryption
    if ($plaintext === $decrypted) {
        echo "Encryption/Decryption successful!" . PHP_EOL;
    } else {
        echo "Encryption/Decryption failed!" . PHP_EOL;
    }

    // Example with Additional Authenticated Data (AAD)
    echo PHP_EOL . "=== Example with AAD ===" . PHP_EOL;
    
    $aad = "This is additional authenticated data";
    $ciphertextWithAad = $sm4gcm->encrypt($plaintext, $aad);
    echo "Ciphertext with AAD (hex): " . CryptoUtils::toHex($ciphertextWithAad) . PHP_EOL;

    $decryptedWithAad = $sm4gcm->decrypt($ciphertextWithAad, $aad);
    echo "Decrypted plaintext with AAD: " . $decryptedWithAad . PHP_EOL;

    // Verify successful decryption with AAD
    if ($plaintext === $decryptedWithAad) {
        echo "Encryption/Decryption with AAD successful!" . PHP_EOL;
    } else {
        echo "Encryption/Decryption with AAD failed!" . PHP_EOL;
    }

} catch (SM4GCMException $e) {
    echo "SM4GCM Error: " . $e->getMessage() . PHP_EOL;
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . PHP_EOL;
}
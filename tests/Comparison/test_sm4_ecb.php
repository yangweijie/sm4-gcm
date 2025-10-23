<?php
// 测试 SM4-ECB 是否正常工作
echo "Testing SM4-ECB...\n";

$key = str_repeat("\x00", 16); // 128位密钥
$plaintext = "Test message for SM4";

echo "Attempting to encrypt with sm4-ecb...\n";
$ciphertext = openssl_encrypt(
    $plaintext,
    'sm4-ecb',
    $key,
    OPENSSL_RAW_DATA
);

if ($ciphertext === false) {
    echo "Encryption failed: " . openssl_error_string() . "\n";
} else {
    echo "Encryption successful\n";
    echo "Ciphertext length: " . strlen($ciphertext) . "\n";
    
    // 尝试解密
    echo "Attempting to decrypt...\n";
    $decrypted = openssl_decrypt(
        $ciphertext,
        'sm4-ecb',
        $key,
        OPENSSL_RAW_DATA
    );
    
    if ($decrypted === false) {
        echo "Decryption failed: " . openssl_error_string() . "\n";
    } else {
        echo "Decryption successful\n";
        echo "Decrypted text: " . $decrypted . "\n";
    }
}

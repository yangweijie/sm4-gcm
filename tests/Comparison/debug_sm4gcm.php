<?php
// 测试 OpenSSL SM4-GCM 支持情况

echo "PHP Version: " . PHP_VERSION . "\n";
echo "OpenSSL Version: " . OPENSSL_VERSION_TEXT . "\n";

// 检查可用的加密方法
$methods = openssl_get_cipher_methods();
$sm4gcmSupported = in_array('sm4-gcm', $methods);
echo "SM4-GCM in cipher methods: " . ($sm4gcmSupported ? 'YES' : 'NO') . "\n";

if ($sm4gcmSupported) {
    echo "SM4-GCM is listed in cipher methods\n";
    
    // 尝试使用 SM4-GCM 进行加密
    $key = str_repeat("\x00", 16); // 128位密钥
    $iv = str_repeat("\x00", 12);  // 96位IV
    $plaintext = "Test message";
    $tag = '';
    
    // 尝试不同的算法名称
    $algorithms = ['sm4-gcm', 'SM4-GCM'];
    $ciphertext = false;
    
    foreach ($algorithms as $algorithm) {
        echo "Attempting to encrypt with $algorithm...\n";
        $ciphertext = openssl_encrypt(
            $plaintext,
            $algorithm,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        if ($ciphertext !== false) {
            echo "Encryption successful with $algorithm\n";
            echo "Ciphertext length: " . strlen($ciphertext) . "\n";
            echo "Tag length: " . strlen($tag) . "\n";
            break;
        } else {
            echo "Encryption failed with $algorithm: " . openssl_error_string() . "\n";
        }
    }
    
    if ($ciphertext === false) {
        echo "All encryption attempts failed\n";
    }
} else {
    echo "SM4-GCM is NOT listed in cipher methods\n";
}

// 列出所有包含 "sm4" 的加密方法
echo "\nSM4-related cipher methods:\n";
foreach ($methods as $method) {
    if (stripos($method, 'sm4') !== false) {
        echo "- $method\n";
    }
}

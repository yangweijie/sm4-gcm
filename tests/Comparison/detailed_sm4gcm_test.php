<?php
// 详细测试 SM4-GCM 问题

echo "PHP Version: " . PHP_VERSION . "\n";
echo "OpenSSL Version: " . OPENSSL_VERSION_TEXT . "\n";

// 测试不同的 GCM 算法
$gcmAlgorithms = ['aes-128-gcm', 'sm4-gcm'];
$key = str_repeat("\x00", 16); // 128位密钥
$iv = str_repeat("\x00", 12);  // 96位IV
$plaintext = "Test message";

foreach ($gcmAlgorithms as $algorithm) {
    echo "\nTesting $algorithm...\n";
    
    // 检查算法是否在支持列表中
    $methods = openssl_get_cipher_methods();
    $supported = in_array($algorithm, $methods);
    echo "Algorithm in methods list: " . ($supported ? 'YES' : 'NO') . "\n";
    
    if ($supported) {
        $tag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            $algorithm,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        if ($ciphertext === false) {
            echo "Encryption failed: " . openssl_error_string() . "\n";
        } else {
            echo "Encryption successful\n";
            echo "Ciphertext length: " . strlen($ciphertext) . "\n";
            echo "Tag length: " . strlen($tag) . "\n";
        }
    }
}

// 检查 OpenSSL 配置
echo "\nOpenSSL configuration:\n";
$cfg = openssl_get_cipher_methods(true); // 获取别名
$gcmMethods = [];
foreach ($cfg as $method) {
    if (strpos($method, 'gcm') !== false) {
        $gcmMethods[] = $method;
    }
}
echo "GCM methods available: " . implode(', ', $gcmMethods) . "\n";
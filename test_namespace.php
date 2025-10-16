<?php

require_once 'vendor/autoload.php';

use yangweijie\SM4GCM\SM4GCM;
use yangweijie\SM4GCM\CryptoUtils;

try {
    echo "Testing CryptoUtils...\n";
    $hex = CryptoUtils::toHex('Hello World');
    echo "Hex: " . $hex . "\n";
    
    echo "Testing SM4GCM class exists...\n";
    $reflection = new ReflectionClass(SM4GCM::class);
    echo "SM4GCM class found: " . $reflection->getName() . "\n";
    
    echo "All tests passed!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
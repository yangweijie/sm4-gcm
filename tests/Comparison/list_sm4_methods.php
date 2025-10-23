<?php
$methods = openssl_get_cipher_methods();
foreach ($methods as $method) {
    if (strpos($method, 'sm4') !== false) {
        echo $method . "\n";
    }
}
<?php

use yangweijie\SM4GCM\Adapter\OpenSSLAdapter;
use yangweijie\SM4GCM\Adapter\AdapterFactory;
use yangweijie\SM4GCM\CryptoUtils;
use yangweijie\SM4GCM\Exceptions\InvalidKeyException;
use yangweijie\SM4GCM\Exceptions\InvalidParameterException;
use yangweijie\SM4GCM\Exceptions\DecryptionException;

test('is openssl sm4gcm supported', function () {
    $supported = AdapterFactory::isOpenSSLSM4GCMsupported();
    // This test will pass or fail depending on whether OpenSSL SM4-GCM is supported
    expect($supported)->toBeBool();
});

test('create openssl adapter', function () {
    if (!AdapterFactory::isOpenSSLSM4GCMsupported()) {
        expect(fn() => AdapterFactory::create(AdapterFactory::ADAPTER_OPENSSL))->toThrow(InvalidParameterException::class);
        return;
    }
    
    $adapter = AdapterFactory::create(AdapterFactory::ADAPTER_OPENSSL);
    expect($adapter)->toBeInstanceOf(OpenSSLAdapter::class);
});

test('create invalid adapter', function () {
    expect(fn() => AdapterFactory::create('invalidadapter'))->toThrow(InvalidParameterException::class);
});

test('openssl adapter encrypt', function () {
    if (!AdapterFactory::isOpenSSLSM4GCMsupported()) {
        $this->markTestSkipped('OpenSSL SM4-GCM is not supported on this system');
        return;
    }

    $testKey = CryptoUtils::toBytes('0123456789ABCDEFFEDCBA9876543210');
    $testIv = CryptoUtils::toBytes('000000000000000000000000');
    $testPlaintext = 'This is a test message for SM4-GCM encryption.';
    $testAad = 'Additional authenticated data';

    $result = OpenSSLAdapter::encrypt(
        $testKey,
        $testIv,
        $testPlaintext,
        $testAad,
        16
    );

    expect($result)->toHaveKey('ciphertext');
    expect($result)->toHaveKey('tag');
    expect(strlen($result['tag']))->toBe(16);
});

test('openssl adapter encrypt invalid key', function () {
    if (!AdapterFactory::isOpenSSLSM4GCMsupported()) {
        $this->markTestSkipped('OpenSSL SM4-GCM is not supported on this system');
        return;
    }

    $testIv = CryptoUtils::toBytes('000000000000000000000000');
    $testPlaintext = 'This is a test message for SM4-GCM encryption.';
    $testAad = 'Additional authenticated data';

    expect(fn() => OpenSSLAdapter::encrypt(
        'invalidkey',
        $testIv,
        $testPlaintext,
        $testAad,
        16
    ))->toThrow(InvalidKeyException::class);
});

test('openssl adapter decrypt', function () {
    if (!AdapterFactory::isOpenSSLSM4GCMsupported()) {
        $this->markTestSkipped('OpenSSL SM4-GCM is not supported on this system');
        return;
    }

    $testKey = CryptoUtils::toBytes('0123456789ABCDEFFEDCBA9876543210');
    $testIv = CryptoUtils::toBytes('000000000000000000000000');
    $testPlaintext = 'This is a test message for SM4-GCM encryption.';
    $testAad = 'Additional authenticated data';

    // First encrypt some data
    $encryptResult = OpenSSLAdapter::encrypt(
        $testKey,
        $testIv,
        $testPlaintext,
        $testAad,
        16
    );

    // Then decrypt it
    $plaintext = OpenSSLAdapter::decrypt(
        $testKey,
        $testIv,
        $encryptResult['ciphertext'],
        $encryptResult['tag'],
        $testAad
    );

    expect($plaintext)->toBe($testPlaintext);
});

test('openssl adapter decrypt invalid tag', function () {
    if (!AdapterFactory::isOpenSSLSM4GCMsupported()) {
        $this->markTestSkipped('OpenSSL SM4-GCM is not supported on this system');
        return;
    }

    $testKey = CryptoUtils::toBytes('0123456789ABCDEFFEDCBA9876543210');
    $testIv = CryptoUtils::toBytes('000000000000000000000000');
    $testPlaintext = 'This is a test message for SM4-GCM encryption.';
    $testAad = 'Additional authenticated data';

    // First encrypt some data
    $encryptResult = OpenSSLAdapter::encrypt(
        $testKey,
        $testIv,
        $testPlaintext,
        $testAad,
        16
    );

    // Try to decrypt with wrong tag
    expect(fn() => OpenSSLAdapter::decrypt(
        $testKey,
        $testIv,
        $encryptResult['ciphertext'],
        'wrongtag12345678',
        $testAad
    ))->toThrow(DecryptionException::class);
});
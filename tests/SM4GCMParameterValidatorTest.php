<?php

use yangweijie\SM4GCM\SM4GCMParameterValidator;
use yangweijie\SM4GCM\Exceptions\InvalidParameterException;

test('validate key valid', function () {
    $key = str_repeat('A', SM4GCMParameterValidator::SM4_KEY_SIZE);
    expect(SM4GCMParameterValidator::validateKey($key))->toBeNull();
});

test('validate key invalid', function () {
    expect(fn() => SM4GCMParameterValidator::validateKey(str_repeat('A', SM4GCMParameterValidator::SM4_KEY_SIZE - 1)))
        ->toThrow(InvalidParameterException::class);
});

test('validate nonce valid', function () {
    $nonce = str_repeat('A', SM4GCMParameterValidator::GCM_DEFAULT_NONCE_SIZE);
    expect(SM4GCMParameterValidator::validateNonce($nonce))->toBeNull();
});

test('validate nonce too short', function () {
    expect(fn() => SM4GCMParameterValidator::validateNonce(str_repeat('A', SM4GCMParameterValidator::GCM_NONCE_MIN_SIZE - 1)))
        ->toThrow(InvalidParameterException::class);
});

test('validate nonce too long', function () {
    expect(fn() => SM4GCMParameterValidator::validateNonce(str_repeat('A', SM4GCMParameterValidator::GCM_NONCE_MAX_SIZE + 1)))
        ->toThrow(InvalidParameterException::class);
});

test('validate tag valid', function () {
    $tag = str_repeat('A', SM4GCMParameterValidator::GCM_TAG_SIZE);
    expect(SM4GCMParameterValidator::validateTag($tag))->toBeNull();
});

test('validate tag invalid', function () {
    expect(fn() => SM4GCMParameterValidator::validateTag(str_repeat('A', SM4GCMParameterValidator::GCM_TAG_SIZE - 1)))
        ->toThrow(InvalidParameterException::class);
});

test('validate aad valid', function () {
    expect(SM4GCMParameterValidator::validateAAD(null))->toBeNull();
    expect(SM4GCMParameterValidator::validateAAD(''))->toBeNull();
    expect(SM4GCMParameterValidator::validateAAD('some aad data'))->toBeNull();
});
<?php

use yangweijie\SM4GCM\SM4GCM;
use yangweijie\SM4GCM\Exceptions\InvalidParameterException;

test('invalid key exception', function () {
    $testIv = hex2bin('000000000000000000000000');
    expect(fn() => new SM4GCM('invalidkey', $testIv))->toThrow(InvalidParameterException::class);
});
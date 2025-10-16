<?php

namespace yangweijie\SM4GCM\Exceptions;

class DecryptionException extends SM4GCMException
{
    public function __construct($message = "Decryption operation failed", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
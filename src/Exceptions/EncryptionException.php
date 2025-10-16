<?php

namespace yangweijie\SM4GCM\Exceptions;

class EncryptionException extends SM4GCMException
{
    public function __construct($message = "Encryption operation failed", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
<?php

namespace yangweijie\SM4GCM\Exceptions;

class InvalidKeyException extends SM4GCMException
{
    public function __construct($message = "Invalid key provided", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
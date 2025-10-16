<?php

namespace yangweijie\SM4GCM\Exceptions;

class InvalidParameterException extends SM4GCMException
{
    public function __construct($message = "Invalid parameter provided", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
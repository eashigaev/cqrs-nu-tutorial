<?php

namespace Codderz\Yoko;

use Throwable;

class CommonException extends \Exception
{
    public static function new($message = "", $code = 0, Throwable $previous = null)
    {
        return new static($message, $code, $previous);
    }
}

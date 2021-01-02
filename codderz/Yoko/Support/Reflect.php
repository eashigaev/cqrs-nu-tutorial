<?php

namespace Codderz\Yoko\Support;

class Reflect
{
    public static function shortClass($class)
    {
        return (new \ReflectionClass($class))->getShortName();
    }
}

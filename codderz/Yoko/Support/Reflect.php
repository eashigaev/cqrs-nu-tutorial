<?php

namespace Codderz\Yoko\Support;

class Reflect
{
    public static function shortClass($class)
    {
        return (new \ReflectionClass($class))->getShortName();
    }

    public static function paramTypes($class, $method)
    {
        $method = new \ReflectionMethod($class, $method);

        return array_map(
            fn($parameter) => $parameter->getType()->getName(),
            $method->getParameters()
        );
    }
}

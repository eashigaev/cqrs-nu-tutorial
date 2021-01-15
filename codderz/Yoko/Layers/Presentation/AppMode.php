<?php

namespace Codderz\Yoko\Layers\Presentation;

use Illuminate\Support\Str;

class AppMode
{
    public static function isApi($requestUri)
    {
        return Str::startsWith($requestUri, config('custom.api.prefix'));
    }

    public static function isWeb($requestUri)
    {
        return !self::isApi($requestUri);
    }
}

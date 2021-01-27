<?php

namespace Codderz\Yoko\Support;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Guid
{
    protected static ?string $mockValue = null;

    public string $value;

    public static function of(string $value)
    {
        $self = new self;
        $self->value = $value;
        return $self;
    }

    public static function generate()
    {
        return static::of(static::uuid());
    }

    public static function uuid()
    {
        return static::$mockValue ?: RamseyUuid::uuid4()->toString();
    }

    public static function mock($value)
    {
        static::$mockValue = $value;
    }
}

<?php

namespace Codderz\Yoko\Support;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Guid
{
    public string $value;

    public static function of(string $value)
    {
        $self = new self;
        $self->value = $value;
        return $self;
    }

    public static function generate()
    {
        return static::of(RamseyUuid::uuid4()->toString());
    }
}

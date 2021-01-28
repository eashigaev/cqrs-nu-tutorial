<?php

namespace Src\Domain\Tab\Commands;

use Codderz\Yoko\Support\Domain\Guid;

class CloseTab
{
    public Guid $id;
    public float $amountPaid;

    public static function of(Guid $id, float $amountPaid)
    {
        $self = new self();
        $self->id = $id;
        $self->amountPaid = $amountPaid;
        return $self;
    }
}

<?php

namespace Src\Domain\Tab\Events;

use Codderz\Yoko\Support\Domain\Guid;

class TabClosed
{
    public Guid $id;
    public float $amountPaid;
    public float $orderValue;
    public float $tipValue;

    public static function of(Guid $id, float $amountPaid, float $orderValue, float $tipValue)
    {
        $self = new self();
        $self->id = $id;
        $self->amountPaid = $amountPaid;
        $self->orderValue = $orderValue;
        $self->tipValue = $tipValue;
        return $self;
    }
}

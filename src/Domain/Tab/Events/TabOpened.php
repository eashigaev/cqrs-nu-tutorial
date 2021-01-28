<?php

namespace Src\Domain\Tab\Events;

use Codderz\Yoko\Layers\Domain\Guid;

class TabOpened
{
    public Guid $id;
    public int $tableNumber;
    public string $waiter;

    public static function of(Guid $id, int $tableNumber, string $waiter)
    {
        $self = new self();
        $self->id = $id;
        $self->tableNumber = $tableNumber;
        $self->waiter = $waiter;
        return $self;
    }
}

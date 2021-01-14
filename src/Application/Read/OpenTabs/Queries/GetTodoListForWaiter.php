<?php

namespace Src\Application\Read\OpenTabs\Queries;

class GetTodoListForWaiter
{
    public string $waiter;

    public static function of(string $waiter)
    {
        $self = new self();
        $self->waiter = $waiter;
        return $self;
    }
}

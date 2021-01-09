<?php

namespace Src\Application\Read\ChiefTodoList\Payloads;

class TodoListItem
{
    public int $menuNumber;
    public string $description;

    public static function of(int $menuNumber, string $description)
    {
        $self = new self;
        $self->menuNumber = $menuNumber;
        $self->description = $description;
        return $self;
    }
}

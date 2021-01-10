<?php

namespace Src\Application\Read\OpenTabs;

use Codderz\Yoko\Contracts\ArrayableInterface;
use Codderz\Yoko\Support\Guid;

class TabTodo implements ArrayableInterface
{
    public Guid $tabId;
    public TableTodo $tableTodo;

    public static function of(Guid $tabId, TableTodo $tableTodo)
    {
        $self = new self;
        $self->tabId = $tabId;
        $self->tableTodo = $tableTodo;
        return $self;
    }

    public function toArray(): array
    {
        return [
            'tabId' => $this->tabId->value,
            'tableTodo' => $this->tableTodo->toArray()
        ];
    }
}

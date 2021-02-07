<?php

namespace Src\Application\Read\ChefTodoList;

use Codderz\Yoko\Layers\Infrastructure\Messenger\Actions\ActionHandlerInterface;
use Codderz\Yoko\Layers\Infrastructure\Messenger\Events\EventHandlerInterface;
use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

interface ChefTodoListInterface extends ActionHandlerInterface, EventHandlerInterface
{
    /** @return Collection<TodoListGroup> */
    public function getTodoList(GetTodoList $query): Collection;
}

<?php

namespace Src\Application\Read\ChefTodoList;

use Codderz\Yoko\Layers\Application\Read\ReadModel\ReadModelHandlerInterface;
use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

interface ChefTodoListInterface extends ReadModelHandlerInterface
{
    /** @return Collection<TodoListGroup> */
    public function getTodoList(GetTodoList $query): Collection;
}

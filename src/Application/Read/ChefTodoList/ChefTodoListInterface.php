<?php

namespace Src\Application\Read\ChefTodoList;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

interface ChefTodoListInterface
{
    /** @return Collection<TodoListGroup> */
    public function getTodoList(GetTodoList $query): Collection;
}

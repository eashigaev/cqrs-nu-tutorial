<?php

namespace Src\Application\Read\ChefTodoList;

use Codderz\Yoko\Layers\Application\Read\ReadModel\ReadModelInterface;
use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

interface ChefTodoListInterface extends ReadModelInterface
{
    /** @return Collection<TodoListGroup> */
    public function getTodoList(GetTodoList $query): Collection;
}

<?php

namespace Src\Application\Read\ChiefTodoList;

use Codderz\Yoko\Layers\Application\Read\ReadModelInterface;
use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChiefTodoList\Payloads\TodoListGroup;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoList;

interface ChiefTodoListInterface extends ReadModelInterface
{
    /** @return Collection<TodoListGroup> */
    public function getTodoList(GetTodoList $query): Collection;
}

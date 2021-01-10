<?php

namespace Src\Application\Read\ChiefTodoList;

use Codderz\Yoko\Layers\Application\Read\ReadModelInterface;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoList;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoListResult;

interface ChiefTodoListInterface extends ReadModelInterface
{
    public function handleGetTodoList(GetTodoList $query): GetTodoListResult;
}

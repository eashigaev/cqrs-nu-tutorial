<?php

namespace Src\Application\Read\ChiefTodoList;

use Codderz\Yoko\Layers\Application\Read\ReadModelInterface;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoList;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoListResult;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;

interface ChiefTodoListInterface extends ReadModelInterface
{
    public function handleGetTodoList(GetTodoList $query): GetTodoListResult;

    public function applyFoodOrdered(FoodOrdered $event);

    public function applyFoodPrepared(FoodPrepared $event);
}

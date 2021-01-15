<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Codderz\Yoko\Layers\Presentation\ApiPresenterTrait;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

class ChefTodoListController extends Controller
{
    use ApiPresenterTrait;

    protected QueryBusInterface $queryBus;

    public function __construct(QueryBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function getTodoList()
    {
        $result = $this->queryBus->handle(
            GetTodoList::of()
        );

        return $this->successApiResponse(
            $result
        );
    }
}

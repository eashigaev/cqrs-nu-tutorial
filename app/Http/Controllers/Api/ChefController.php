<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusInterface;
use Codderz\Yoko\Layers\Domain\Guid;
use Codderz\Yoko\Layers\Presentation\ApiPresenterTrait;
use Codderz\Yoko\Support\Collection;
use Illuminate\Http\Request;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Domain\Tab\Commands\MarkFoodPrepared;

class ChefController extends Controller
{
    use ApiPresenterTrait;

    protected QueryBusInterface $queryBus;
    protected CommandBusInterface $commandBus;

    public function __construct(QueryBusInterface $queryBus, CommandBusInterface $commandBus)
    {
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
    }

    public function getTodoList()
    {
        $query = GetTodoList::of();

        $result = $this->queryBus->handle($query);

        return $this->successApiResponse($result);
    }

    public function markFoodPrepared(Request $request)
    {
        $command = MarkFoodPrepared::of(
            Guid::of($request->tabId),
            Collection::of($request->menuNumbers)
        );

        $this->commandBus->handle($command);

        return $this->successApiResponse();
    }
}

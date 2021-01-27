<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusInterface;
use Codderz\Yoko\Layers\Presentation\ApiPresenterTrait;
use Codderz\Yoko\Support\Guid;
use Illuminate\Http\Request;
use Src\Domain\Tab\Commands\OpenTab;

class TabController extends Controller
{
    use ApiPresenterTrait;

    protected QueryBusInterface $queryBus;
    protected CommandBusInterface $commandBus;

    public function __construct(QueryBusInterface $queryBus, CommandBusInterface $commandBus)
    {
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
    }

    public function open(Request $request)
    {
        $tabId = Guid::generate();

        $command = OpenTab::of(
            $tabId,
            $request->tableNumber,
            $request->waiter
        );

        $this->commandBus->handle($command);

        return $this->successApiResponse($tabId->value);
    }
}

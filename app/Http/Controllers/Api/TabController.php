<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusInterface;
use Codderz\Yoko\Layers\Presentation\ApiPresenterTrait;
use Codderz\Yoko\Support\Guid;
use Illuminate\Http\Request;
use Src\Application\StaticData;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\OrderedItem;

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

    public function openTab(Request $request)
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

    public function placeOrder(Request $request)
    {
        $orderedItems = StaticData::products()
            ->whereIn('menuNumber', $request->menuNumbers)
            ->map(fn($item) => OrderedItem::ofArray($item));

        $command = PlaceOrder::of(
            Guid::of($request->tabId),
            $orderedItems
        );

        $this->commandBus->handle($command);

        return $this->successApiResponse();
    }

    public function markServed(Request $request)
    {
        $products = StaticData::products()
            ->whereIn('menuNumber', $request->menuNumbers);

        $drinksCommand = MarkDrinksServed::of(
            Guid::of($request->tabId),
            $products->where('isDrink', true)->pluck('menuNumber')
        );
        $this->commandBus->handle($drinksCommand);

        $foodCommand = MarkFoodServed::of(
            Guid::of($request->tabId),
            $products->where('isDrink', false)->pluck('menuNumber')
        );
        $this->commandBus->handle($foodCommand);

        return $this->successApiResponse();
    }

    public function closeTab(Request $request)
    {
        $command = CloseTab::of(
            Guid::of($request->tabId),
            $request->amountPaid
        );

        $this->commandBus->handle($command);

        return $this->successApiResponse();
    }
}

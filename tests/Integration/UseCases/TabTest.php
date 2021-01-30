<?php

namespace Tests\Integration\UseCases;

use Codderz\Yoko\Support\Collection;
use Illuminate\Database\QueryException;
use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Application\Read\OpenTabs\Queries\GetInvoiceForTable;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\MarkFoodPrepared;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;

class TabTest extends TestCase
{
    public function testCanNotOpenManyTabsForSingleTable()
    {
        $this->expectException(QueryException::class);

        $this->handleCommands([
            OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter),
            OpenTab::of($this->bTabId, $this->aTable, $this->aWaiter),
        ]);
    }

    public function testCanViewActiveTableNumbers()
    {
        $empty = $this->queryBus()->handle(GetActiveTableNumbers::of());
        $this->assertEquals(0, $empty->count());

        $this->handleCommands([
            OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter),
            OpenTab::of($this->bTabId, $this->bTable, $this->aWaiter),
        ]);

        $numbers = $this->queryBus()->handle(GetActiveTableNumbers::of());
        $this->assertEquals([$this->aTable, $this->bTable], $numbers->toArray());
    }

    public function t1estCanViewTabInvoice()
    {
        $this->handleCommands([
            OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter),
            PlaceOrder::of($this->aTabId, Collection::of([$this->drink1, $this->food1])),
            MarkDrinksServed::of($this->aTabId, Collection::of([$this->drink1->menuNumber])),
            MarkFoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber])),
            MarkFoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber])),
            CloseTab::of($this->aTabId, $this->drink1->price + $this->food1->price + 2)
        ]);

        $invoice = $this->queryBus()->handle(GetInvoiceForTable::of($this->aTable));

        dd($invoice);
    }
}

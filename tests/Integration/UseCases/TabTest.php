<?php

namespace Tests\Integration\UseCases;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Application\Read\ChefTodoList\TodoListGroup;
use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Application\Read\OpenTabs\Queries\GetInvoiceForTable;
use Src\Application\Read\OpenTabs\Queries\GetTabForTable;
use Src\Application\Read\OpenTabs\Queries\GetTodoListForWaiter;
use Src\Application\Read\OpenTabs\TabInvoice;
use Src\Application\Read\OpenTabs\TabStatus;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\MarkFoodPrepared;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Exceptions\TableAlreadyOpened;

class TabTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFixture();
        $this->commandBatch([
            OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter),
            PlaceOrder::of($this->aTabId, Collection::of([$this->drink1, $this->food1])),
            MarkDrinksServed::of($this->aTabId, Collection::of([$this->drink1->menuNumber])),
            MarkFoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber])),
            MarkFoodServed::of($this->aTabId, Collection::of([$this->food1->menuNumber]))
        ]);
    }

    /** @group a */
    public function testCanOpenTab()
    {
        $tables = $this->queryBus()->handle(GetActiveTableNumbers::of());

        $this->assertEquals(1, $tables->count());
    }

    public function testCanNotOpenTabForAlreadyOpenedTable()
    {
        $this->expectException(TableAlreadyOpened::class);

        $this->commandBatch([
            OpenTab::of($this->bTabId, $this->aTable, $this->aWaiter),
        ]);
    }

    public function testCanCloseTab()
    {
        $this->commandBatch([
            CloseTab::of($this->aTabId, $this->drink1->price + $this->food1->price),
        ]);

        $empty = $this->queryBus()->handle(GetActiveTableNumbers::of());
        $this->assertEquals(0, $empty->count());
    }

    public function testCanGetTabInvoice()
    {
        /** @var TabInvoice $invoice */
        $invoice = $this->queryBus()->handle(GetInvoiceForTable::of($this->aTable));

        $this->assertEquals(2, $invoice->items->count());
        $this->assertEquals(false, $invoice->hasUnservedItems);
        $this->assertEquals($this->drink1->price + $this->food1->price, $invoice->total);
    }

    public function testCanGetTabStatus()
    {
        /** @var TabStatus $status */
        $status = $this->queryBus()->handle(GetTabForTable::of($this->aTable));

        $this->assertEquals($this->aTable, $status->tableNumber);
        $this->assertEquals(2, $status->served->count());
    }

    public function testCanGetWaiterTodoList()
    {
        $this->commandBatch([
            PlaceOrder::of($this->aTabId, Collection::of([$this->drink1, $this->drink2])),
            OpenTab::of($this->bTabId, $this->bTable, $this->aWaiter),
            PlaceOrder::of($this->bTabId, Collection::of([$this->drink2]))
        ]);

        /** @var Collection<TodoListGroup> $chefTodoList */
        $waiterTodoList = $this->queryBus()->handle(GetTodoListForWaiter::of($this->aWaiter));

        $this->assertEquals(2, $waiterTodoList[$this->aTable]->count());
        $this->assertEquals(1, $waiterTodoList[$this->bTable]->count());
    }

    public function testCanManageChefTodoList()
    {
        $this->commandBus()->handle(
            PlaceOrder::of($this->aTabId, Collection::of([$this->food2]))
        );

        /** @var Collection<TodoListGroup> $chefTodoList */
        $chefTodoList = $this->queryBus()->handle(GetTodoList::of());
        $this->assertEquals(1, $chefTodoList->count());

        $this->commandBus()->handle(
            MarkFoodPrepared::of($this->aTabId, Collection::of([$this->food2->menuNumber]))
        );

        /** @var Collection<TodoListGroup> $chefTodoList */
        $chefTodoList = $this->queryBus()->handle(GetTodoList::of());
        $this->assertEquals(0, $chefTodoList->count());
    }
}

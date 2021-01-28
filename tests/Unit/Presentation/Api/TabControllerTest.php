<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Layers\Domain\Guid;
use Src\Application\Read\OpenTabs\Queries\GetInvoiceForTable;
use Src\Application\Read\OpenTabs\Queries\GetTabForTable;
use Src\Application\StaticData;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\OrderedItem;

class TabControllerTest extends TestCase
{
    public function testCanOpenTab()
    {
        Guid::mock($this->aTabId->value);

        $this
            ->mockCommandBus()
            ->with(OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter));

        $this
            ->post('/api/tab/open', [
                'tableNumber' => $this->aTable,
                'waiter' => $this->aWaiter
            ])
            ->assertStatus(200)
            ->assertJsonFragment(['payload' => $this->aTabId->value]);
    }

    public function testCanPlaceOrder()
    {
        $orderedItems = StaticData::products()
            ->take(2)
            ->map(fn($item) => OrderedItem::ofArray($item));

        $this
            ->mockCommandBus()
            ->with(PlaceOrder::of($this->aTabId, $orderedItems));

        $this
            ->post('/api/tab/order', [
                'tabId' => $this->aTabId->value,
                'menuNumbers' => $orderedItems->pluck('menuNumber')
            ])
            ->assertStatus(200);
    }

    public function testCanMarkServed()
    {
        $products = StaticData::products();

        $this
            ->mockCommandBus($this->exactly(2))
            ->withConsecutive(
                [MarkDrinksServed::of($this->aTabId, $products->where('isDrink', true)->pluck('menuNumber'))],
                [MarkFoodServed::of($this->aTabId, $products->where('isDrink', false)->pluck('menuNumber'))]
            );

        $this
            ->post('/api/tab/serve', [
                'tabId' => $this->aTabId->value,
                'menuNumbers' => $products->pluck('menuNumber')
            ])
            ->assertStatus(200);
    }

    public function testCanCloseTab()
    {
        $this
            ->mockCommandBus()
            ->with(CloseTab::of($this->aTabId, 999.99));

        $this
            ->post('/api/tab/close', [
                'tabId' => $this->aTabId->value,
                'amountPaid' => 999.99
            ])
            ->assertStatus(200);
    }

    public function testCanGetTabForTable()
    {
        $payload = Guid::uuid();

        $this
            ->mockQueryBus()
            ->with(GetTabForTable::of($this->aTable))
            ->willReturn($payload);

        $this
            ->get('/api/tab/status/' . $this->aTable)
            ->assertStatus(200)
            ->assertJsonFragment(['payload' => $payload]);
    }

    public function testCanGetInvoiceForTable()
    {
        $payload = Guid::uuid();

        $this
            ->mockQueryBus()
            ->with(GetInvoiceForTable::of($this->aTable))
            ->willReturn($payload);

        $this
            ->get('/api/tab/invoice/' . $this->aTable)
            ->assertStatus(200)
            ->assertJsonFragment(['payload' => $payload]);
    }
}

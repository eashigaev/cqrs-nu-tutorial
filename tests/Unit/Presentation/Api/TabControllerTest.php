<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Support\Guid;
use Src\Application\StaticData;
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
            ->with(OpenTab::of(
                $this->aTabId, $this->aTable, $this->aWaiter
            ));

        $this
            ->post('/api/tab/open', [
                'tableNumber' => $this->aTable,
                'waiter' => $this->aWaiter
            ])
            ->assertStatus(200)
            ->assertJsonFragment([
                'payload' => $this->aTabId->value
            ]);
    }

    public function testCanPlaceOrder()
    {
        $orderedItems = StaticData::products()
            ->take(2)
            ->map(fn($item) => OrderedItem::ofArray($item));

        $this
            ->mockCommandBus()
            ->with(PlaceOrder::of(
                $this->aTabId, $orderedItems
            ));

        $this
            ->post('/api/tab/order', [
                'tabId' => $this->aTabId->value,
                'menuNumbers' => $orderedItems->pluck('menuNumber')
            ])
            ->assertStatus(200);
    }
}

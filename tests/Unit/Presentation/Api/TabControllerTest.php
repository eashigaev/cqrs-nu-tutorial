<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;

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
}

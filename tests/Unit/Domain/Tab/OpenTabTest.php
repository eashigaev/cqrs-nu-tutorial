<?php

namespace Tests\Unit\Domain\Tab;

use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\TabAlreadyClosed;
use Src\Domain\Tab\Exceptions\TabAlreadyOpen;
use Src\Domain\Tab\TabAggregate;

class OpenTabTest extends TabTestCase
{
    public function testCanOpenTab()
    {
        $aggregate = TabAggregate::fromEvents()
            ->handle(
                OpenTab::of($this->tabId, $this->table, $this->waiter)
            );

        $this->assertReleasedEvents($aggregate, [
            TabOpened::of($this->tabId, $this->table, $this->waiter)
        ]);
    }

    public function testCanNotOpenTabTwice()
    {
        $this->expectExceptionObject(TabAlreadyOpen::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter)
        ])
            ->handle(
                OpenTab::of($this->tabId, $this->table, $this->waiter)
            );
    }

    public function testCanNotOpenClosedTab()
    {
        $this->expectExceptionObject(TabAlreadyClosed::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->tabId, $this->table, $this->waiter),
            TabClosed::of($this->tabId, 0, 0, 0),
        ])
            ->handle(
                OpenTab::of($this->tabId, $this->table, $this->waiter)
            );
    }
}

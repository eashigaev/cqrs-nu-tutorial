<?php

namespace Tests\Unit\Domain\Tab;

use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\TabAlreadyClosed;
use Src\Domain\Tab\Exceptions\TabAlreadyOpen;
use Src\Domain\Tab\TabAggregate;

class OpenTabTest extends TestCase
{
    public function testCanOpenTab()
    {
        $aggregate = TabAggregate::fromEvents()
            ->handle(
                OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter)
            );

        $this->assertReleasedEvents($aggregate, [
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter)
        ]);
    }

    public function testCanNotOpenTabTwice()
    {
        $this->expectExceptionObject(TabAlreadyOpen::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter)
        ])
            ->handle(
                OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter)
            );
    }

    public function testCanNotOpenClosedTab()
    {
        $this->expectExceptionObject(TabAlreadyClosed::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
            TabClosed::of($this->aTabId, 0, 0, 0),
        ])
            ->handle(
                OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter)
            );
    }
}

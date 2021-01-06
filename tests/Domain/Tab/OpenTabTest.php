<?php

namespace Tests\Domain\Tab;

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
                OpenTab::of($this->testId, $this->testTable, $this->testWaiter)
            );

        $this->assertReleasedEvents($aggregate, [
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ]);
    }

    public function testCanNotOpenTabTwice()
    {
        $this->expectExceptionObject(TabAlreadyOpen::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter)
        ])
            ->handle(
                OpenTab::of($this->testId, $this->testTable, $this->testWaiter)
            );
    }

    public function testCanNotOpenClosedTab()
    {
        $this->expectExceptionObject(TabAlreadyClosed::new());

        TabAggregate::fromEvents([
            TabOpened::of($this->testId, $this->testTable, $this->testWaiter),
            TabClosed::of($this->testId, 0, 0, 0),
        ])
            ->handle(
                OpenTab::of($this->testId, $this->testTable, $this->testWaiter)
            );
    }
}

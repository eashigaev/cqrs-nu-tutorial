<?php

namespace Tests\Unit\Domain\Tab;

use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\TabAggregate;

class OpenTabTest extends TestCase
{
    public function testCanOpenTab()
    {
        $aggregate = TabAggregate::openTab(
            OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter)
        );

        $this->assertReleasedEvents($aggregate, [
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter)
        ]);
    }
}

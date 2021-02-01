<?php

namespace Tests\Unit\Domain\Tab;

use Codderz\Yoko\Support\Collection;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Events\TabOpened;
use Src\Domain\Tab\Exceptions\TableAlreadyOpened;
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

    public function testCanNotOpenTabForAlreadyOpenedTable()
    {
        $this->expectException(TableAlreadyOpened::class);

        $aggregate = TabAggregate::openTab(
            OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter),
            Collection::of([$this->aTable])
        );

        $this->assertReleasedEvents($aggregate, [
            TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter)
        ]);
    }
}

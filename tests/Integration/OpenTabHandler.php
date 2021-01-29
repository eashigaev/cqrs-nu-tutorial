<?php

namespace Tests\Integration;

use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\TabAggregate;

class OpenTabHandler extends TestCase
{
    public function testCanOpenTab()
    {
        $command = OpenTab::of($this->aTabId, $this->aTable, $this->aWaiter);

        $tab = TabAggregate::fromEvents();
        $this->tabRepository->save($tab);

        $this->commandBus()->handle($command);

        $tab->handle($command);

        $this->assertEquals($this->tabRepository->ofId($tab->tabId), $tab);
        $this->assertEvents($this->eventBus()->emitted(), $tab);
    }
}

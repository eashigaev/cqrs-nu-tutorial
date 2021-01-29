<?php

namespace Tests\Unit\Application\Read\OpenTabs;

use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;

class GetActiveTableNumbersTest extends TestCase
{
    public function testCanGetActiveTableNumbers()
    {
        $result = $this
            ->openTabs([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                TabOpened::of($this->bTabId, $this->bTable, $this->aWaiter),
            ])
            ->getActiveTableNumbers(GetActiveTableNumbers::of());

        $this->assertResult($result, [$this->aTable, $this->bTable]);
    }

    public function testCanGetOnlyActiveTableNumbers()
    {
        $result = $this
            ->openTabs([
                TabOpened::of($this->aTabId, $this->aTable, $this->aWaiter),
                TabOpened::of($this->bTabId, $this->bTable, $this->aWaiter),
                TabClosed::of($this->bTabId, 0, 0, 0)
            ])
            ->getActiveTableNumbers(GetActiveTableNumbers::of());

        $this->assertResult($result, [$this->aTable]);
    }
}

<?php

namespace Tests\Application\Read\OpenTabs;

use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;

class GetActiveTableNumbersTest extends OpenTabsTestCase
{
    public function testCanGetActiveTableNumbers()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->waiter1),
                TabOpened::of($this->tabId2, $this->table2, $this->waiter1),
            ])
            ->handle(GetActiveTableNumbers::of());

        $this->assertResult($result, [$this->table1, $this->table2]);
    }

    public function testCanGetOnlyActiveTableNumbers()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->waiter1),
                TabOpened::of($this->tabId2, $this->table2, $this->waiter1),
                TabClosed::of($this->tabId2, 0, 0, 0)
            ])
            ->handle(GetActiveTableNumbers::of());

        $this->assertResult($result, [$this->table1]);
    }
}

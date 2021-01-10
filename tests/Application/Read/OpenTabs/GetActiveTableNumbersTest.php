<?php

namespace Tests\Application\Read\OpenTabs;

use Codderz\Yoko\Layers\Application\Read\Testing\ReadTestTrait;
use Codderz\Yoko\Support\Guid;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoListResult;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;
use Tests\TestCase;

class GetActiveTableNumbersTest extends TestCase
{
    use DatabaseTransactions;
    use ReadTestTrait;

    protected OpenTabsInterface $openTabs;

    protected Guid $tabId1, $tabId2;
    protected int $table1, $table2;
    protected string $testWaiter;

    public function setUp(): void
    {
        parent::setUp();

        $this->tabId1 = Guid::of('tab-123');
        $this->tabId2 = Guid::of('tab-456');
        $this->table1 = 21;
        $this->table2 = 87;
        $this->testWaiter = 'Steven';

        $this->openTabs = $this->app->make(OpenTabsInterface::class);
    }

    public function testCanGetActiveTableNumbers()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->testWaiter),
                TabOpened::of($this->tabId2, $this->table2, $this->testWaiter),
            ])
            ->handle(GetActiveTableNumbers::of());

        $this->assertResult($result, [$this->table1, $this->table2]);
    }

    public function testCanGetOnlyActiveTableNumbers()
    {
        $result = $this->openTabs
            ->withEvents([
                TabOpened::of($this->tabId1, $this->table1, $this->testWaiter),
                TabOpened::of($this->tabId2, $this->table2, $this->testWaiter),
                TabClosed::of($this->tabId2, 0, 0, 0)
            ])
            ->handle(GetActiveTableNumbers::of());

        $this->assertResult($result, [$this->table1]);
    }
}

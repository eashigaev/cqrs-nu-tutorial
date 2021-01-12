<?php

namespace Tests\Application\Read\OpenTabs;

use Codderz\Yoko\Layers\Application\Read\Testing\ReadTestTrait;
use Codderz\Yoko\Support\Guid;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Domain\Tab\OrderedItem;
use Tests\TestCase;

abstract class OpenTabsTestCase extends TestCase
{
    use DatabaseMigrations;
    use ReadTestTrait;

    protected Guid $tabId1, $tabId2;
    protected int $table1, $table2;
    protected string $waiter;

    protected OrderedItem $drink1, $drink2, $food1, $food2, $food3;

    protected OpenTabsInterface $openTabs;

    public function setUp(): void
    {
        parent::setUp();

        $this->tabId1 = Guid::of('tab-123');
        $this->tabId2 = Guid::of('tab-456');
        $this->table1 = 21;
        $this->table2 = 87;
        $this->waiter = 'Steven';

        $this->drink1 = OrderedItem::of(4, 'Sprite', true, 5.00);
        $this->drink2 = OrderedItem::of(10, 'Beer', true, 3.00);
        $this->food1 = OrderedItem::of(16, 'Beef Noodles', false, 10.00);
        $this->food2 = OrderedItem::of(25, 'Vegetable Curry', false, 7.00);
        $this->food3 = OrderedItem::of(34, 'Vegas', false, 5.00);

        $this->openTabs = $this->app->make(OpenTabsInterface::class);
    }
}

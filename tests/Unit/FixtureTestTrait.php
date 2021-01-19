<?php

namespace Tests\Unit;

use Codderz\Yoko\Support\Guid;
use Src\Domain\Tab\OrderedItem;

trait FixtureTestTrait
{
    protected Guid $aTabId, $bTabId;
    protected int $aTable, $bTable;
    protected string $aWaiter, $bWaiter;
    protected OrderedItem $drink1, $drink2, $food1, $food2, $food3;

    public function setUpFixture(): void
    {
        $this->aTabId = Guid::of('tab-123');
        $this->bTabId = Guid::of('tab-456');

        $this->aTable = 21;
        $this->bTable = 87;

        $this->aWaiter = 'Steven';
        $this->bWaiter = 'John';

        $this->drink1 = OrderedItem::of(4, 'Sprite', true, 5.00);
        $this->drink2 = OrderedItem::of(10, 'Beer', true, 3.00);
        $this->food1 = OrderedItem::of(16, 'Beef Noodles', false, 10.00);
        $this->food2 = OrderedItem::of(25, 'Vegetable Curry', false, 7.00);
        $this->food3 = OrderedItem::of(34, 'Vegas Steak', false, 5.00);
    }
}

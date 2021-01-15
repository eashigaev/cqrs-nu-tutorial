<?php

namespace Tests\Domain\Tab;

use Codderz\Yoko\Layers\Domain\DomainTestTrait;
use Codderz\Yoko\Support\Guid;
use Src\Domain\Tab\OrderedItem;
use Tests\TestCase;

abstract class TabTestCase extends TestCase
{
    use DomainTestTrait;

    protected Guid $tabId;
    protected int $table;
    protected string $waiter;

    protected OrderedItem $drink1, $drink2, $food1, $food2;

    public function setUp(): void
    {
        $this->tabId = Guid::of('tab-123');
        $this->table = 42;
        $this->waiter = 'Steven';

        $this->drink1 = OrderedItem::of(4, 'Sprite', true, 5.00);
        $this->drink2 = OrderedItem::of(10, 'Beer', true, 3.00);
        $this->food1 = OrderedItem::of(16, 'Beef Noodles', false, 10.00);
        $this->food2 = OrderedItem::of(25, 'Vegetable Curry', false, 10.00);
    }
}

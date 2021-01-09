<?php

namespace Tests\Domain\Tab;

use Codderz\Yoko\Layers\Domain\Testing\DomainTestTrait;
use Codderz\Yoko\Support\Guid;
use Src\Domain\Tab\OrderedItem;
use Tests\TestCase;

abstract class TabTestCase extends TestCase
{
    use DomainTestTrait;

    protected Guid $testId;
    protected int $testTable;
    protected string $testWaiter;

    protected OrderedItem $testDrink1, $testDrink2, $testFood1, $testFood2;

    public function setUp(): void
    {
        $this->testId = Guid::of('tab-123');
        $this->testTable = 42;
        $this->testWaiter = 'Derek';

        $this->testDrink1 = OrderedItem::of(4, 'Sprite', true, 5.00);
        $this->testDrink2 = OrderedItem::of(10, 'Beer', true, 3.00);
        $this->testFood1 = OrderedItem::of(16, 'Beef Noodles', false, 10.00);
        $this->testFood2 = OrderedItem::of(25, 'Vegetable Curry', false, 10.00);
    }
}

<?php

namespace Tests\Application\Read\ChiefTodoList;

use Codderz\Yoko\Layers\Application\Read\Testing\ReadTestTrait;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Src\Application\Read\ChiefTodoList\ChiefTodoListInterface;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoList;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoListResult;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\OrderedItem;
use Tests\TestCase;

class GetTodoListTest extends TestCase
{
    use DatabaseTransactions;
    use ReadTestTrait;

    protected ChiefTodoListInterface $chiefTodoList;

    protected Guid $testId1;
    protected Guid $testId2;

    protected OrderedItem $testDrink1, $testDrink2, $testFood1, $testFood2;

    public function setUp(): void
    {
        parent::setUp();

        $this->testId1 = Guid::of('tab-123');
        $this->testId2 = Guid::of('tab-456');

        $this->testDrink1 = OrderedItem::of(4, 'Sprite', true, 5.00);
        $this->testDrink2 = OrderedItem::of(10, 'Beer', true, 3.00);
        $this->testFood1 = OrderedItem::of(16, 'Beef Noodles', false, 10.00);
        $this->testFood2 = OrderedItem::of(25, 'Vegetable Curry', false, 10.00);

        $this->chiefTodoList = $this->app->make(ChiefTodoListInterface::class);

    }

    public function testCanGetFoodListToPrepare()
    {
        $result = $this->chiefTodoList
            ->withEvents([
                FoodOrdered::of($this->testId1, Collection::make([$this->testFood1, $this->testFood2])),
                FoodOrdered::of($this->testId2, Collection::make([$this->testFood1])),
            ])
            ->handle(GetTodoList::of());

        $this->assertResult($result, [
            [
                'tabId' => $this->testId1->value,
                'items' => [
                    ['menuNumber' => $this->testFood1->menuNumber, 'description' => $this->testFood1->description],
                    ['menuNumber' => $this->testFood2->menuNumber, 'description' => $this->testFood2->description],
                ]
            ],
            [
                'tabId' => $this->testId2->value,
                'items' => [
                    ['menuNumber' => $this->testFood1->menuNumber, 'description' => $this->testFood1->description],
                ]
            ],
        ]);
    }

    public function testCanNotGetPreparedItems()
    {
        $result = $this->chiefTodoList
            ->withEvents([
                FoodOrdered::of($this->testId1, Collection::make([$this->testFood1, $this->testFood2])),
                FoodPrepared::of($this->testId1, Collection::make([
                    $this->testFood1->menuNumber, $this->testFood2->menuNumber
                ])),
            ])
            ->handle(GetTodoList::of());

        $this->assertResult($result, []);
    }
}

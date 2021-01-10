<?php

namespace Tests\Application\Read\ChiefTodoList;

use Codderz\Yoko\Layers\Application\Read\Testing\ReadTestTrait;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Src\Application\Read\ChiefTodoList\ChiefTodoListInterface;
use Src\Application\Read\ChiefTodoList\Queries\GetTodoList;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\OrderedItem;
use Tests\TestCase;

class GetTodoListTest extends TestCase
{
    use DatabaseTransactions;
    use ReadTestTrait;

    protected ChiefTodoListInterface $chiefTodoList;

    protected Guid $tabId1, $tabId2;
    protected OrderedItem $drink1, $drink2, $food1, $food2;

    public function setUp(): void
    {
        parent::setUp();

        $this->tabId1 = Guid::of('tab-123');
        $this->tabId2 = Guid::of('tab-456');

        $this->drink1 = OrderedItem::of(4, 'Sprite', true, 5.00);
        $this->drink2 = OrderedItem::of(10, 'Beer', true, 3.00);
        $this->food1 = OrderedItem::of(16, 'Beef Noodles', false, 10.00);
        $this->food2 = OrderedItem::of(25, 'Vegetable Curry', false, 10.00);

        $this->chiefTodoList = $this->app->make(ChiefTodoListInterface::class);
    }

    public function testCanGetFoodListToPrepare()
    {
        $result = $this->chiefTodoList
            ->withEvents([
                FoodOrdered::of($this->tabId1, Collection::make([$this->food1, $this->food2])),
                FoodOrdered::of($this->tabId2, Collection::make([$this->food1])),
            ])
            ->handle(GetTodoList::of());

        $this->assertResult($result, [
            [
                'tabId' => $this->tabId1->value,
                'items' => [
                    ['menuNumber' => $this->food1->menuNumber, 'description' => $this->food1->description],
                    ['menuNumber' => $this->food2->menuNumber, 'description' => $this->food2->description],
                ]
            ],
            [
                'tabId' => $this->tabId2->value,
                'items' => [
                    ['menuNumber' => $this->food1->menuNumber, 'description' => $this->food1->description],
                ]
            ],
        ]);
    }

    public function testCanNotGetPreparedItems()
    {
        $result = $this->chiefTodoList
            ->withEvents([
                FoodOrdered::of($this->tabId1, Collection::make([$this->food1, $this->food2])),
                FoodPrepared::of($this->tabId1, Collection::make([$this->food1->menuNumber, $this->food2->menuNumber])),
            ])
            ->handle(GetTodoList::of());

        $this->assertResult($result, []);
    }
}

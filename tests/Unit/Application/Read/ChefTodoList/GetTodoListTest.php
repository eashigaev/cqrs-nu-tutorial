<?php

namespace Tests\Unit\Application\Read\ChefTodoList;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;

class GetTodoListTest extends TestCase
{
    public function testCanGetFoodListToPrepare()
    {
        $result = $this
            ->chefTodoList([
                FoodOrdered::of($this->aTabId, Collection::of([$this->food1, $this->food2])),
                FoodOrdered::of($this->bTabId, Collection::of([$this->food1])),
            ])
            ->getTodoList(GetTodoList::of());

        $this->assertResult($result, [
            [
                'tabId' => $this->aTabId->value,
                'items' => [
                    ['menuNumber' => $this->food1->menuNumber, 'description' => $this->food1->description],
                    ['menuNumber' => $this->food2->menuNumber, 'description' => $this->food2->description],
                ]
            ],
            [
                'tabId' => $this->bTabId->value,
                'items' => [
                    ['menuNumber' => $this->food1->menuNumber, 'description' => $this->food1->description],
                ]
            ],
        ]);
    }

    public function testCanGetEmptyListWhenFoodNotOrdered()
    {
        $result = $this
            ->chefTodoList()
            ->getTodoList(GetTodoList::of());

        $this->assertResult($result, []);
    }

    public function testCanGetEmptyListWhenFoodAlreadyPrepared()
    {
        $result = $this
            ->chefTodoList([
                FoodOrdered::of($this->aTabId, Collection::of([$this->food1, $this->food2])),
                FoodPrepared::of($this->aTabId, Collection::of([$this->food1->menuNumber, $this->food2->menuNumber])),
            ])
            ->getTodoList(GetTodoList::of());

        $this->assertResult($result, []);
    }
}

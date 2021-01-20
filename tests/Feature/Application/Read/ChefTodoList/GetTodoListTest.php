<?php

namespace Tests\Feature\Application\Read\ChefTodoList;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;

class GetTodoListTest extends TestCase
{
    public function testCanGetFoodListToPrepare()
    {
        $result = $this->chefTodoList
            ->withEvents([
                FoodOrdered::of($this->aTabId, Collection::make([$this->food1, $this->food2])),
                FoodOrdered::of($this->bTabId, Collection::make([$this->food1])),
            ])
            ->handle(GetTodoList::of());

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
        $result = $this->chefTodoList
            ->handle(GetTodoList::of());

        $this->assertResult($result, []);
    }

    public function testCanGetEmptyListWhenFoodAlreadyPrepared()
    {
        $result = $this->chefTodoList
            ->withEvents([
                FoodOrdered::of($this->aTabId, Collection::make([$this->food1, $this->food2])),
                FoodPrepared::of($this->aTabId, Collection::make([$this->food1->menuNumber, $this->food2->menuNumber])),
            ])
            ->handle(GetTodoList::of());

        $this->assertResult($result, []);
    }
}

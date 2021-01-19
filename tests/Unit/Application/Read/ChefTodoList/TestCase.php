<?php

namespace Tests\Unit\Application\Read\ChefTodoList;

use Codderz\Yoko\Layers\Application\Read\ReadTestTrait;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerTestTrait;
use Codderz\Yoko\Support\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Tests\TestCase as BaseTestCase;
use Tests\Unit\FixtureTestTrait;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations,
        FixtureTestTrait,
        ReadTestTrait,
        ContainerTestTrait;

    protected ChefTodoListInterface $chefTodoList;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFixture();
        $this->chefTodoList = $this->container()->make(ChefTodoListInterface::class);
    }

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

    public function testCanNotGetPreparedItems()
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

<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Domain\Tab\Commands\MarkFoodPrepared;

class ChefControllerTest extends TestCase
{
    public function testCanQueryGetTodoList()
    {
        $this
            ->mockQueryBus()
            ->with(GetTodoList::of())
            ->willReturn([1, 2, 3]);

        $this
            ->get('/api/chef/todo-list')
            ->assertStatus(200)
            ->assertJsonFragment([
                'payload' => [1, 2, 3]
            ]);
    }

    public function testCanCommandMarkFoodPrepared()
    {
        $this
            ->mockCommandBus()
            ->with(MarkFoodPrepared::of(
                $this->aTabId, Collection::make([$this->food1->menuNumber, $this->food2->menuNumber])
            ));

        $this
            ->post('/api/chef/mark-food-prepared', [
                'tabId' => $this->aTabId->value,
                'menuNumbers' => [$this->food1->menuNumber, $this->food2->menuNumber]
            ])
            ->assertStatus(200);
    }
}

<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Domain\Tab\Commands\MarkFoodPrepared;

class ChefControllerTest extends TestCase
{
    public function testCanGetTodoList()
    {
        $this
            ->mockQueryBus()
            ->with(GetTodoList::of())
            ->willReturn([1, 2, 3]);

        $this
            ->get('/api/chef/todo-list')
            ->assertStatus(200)
            ->assertJsonFragment(['payload' => [1, 2, 3]]);
    }

    public function testCanMarkFoodPrepared()
    {
        $this
            ->mockCommandBus()
            ->with(MarkFoodPrepared::of(
                $this->aTabId, Collection::of([$this->food1->menuNumber, $this->food2->menuNumber])
            ));

        $this
            ->post('/api/chef/prepare', [
                'tabId' => $this->aTabId->value,
                'menuNumbers' => [$this->food1->menuNumber, $this->food2->menuNumber]
            ])
            ->assertStatus(200);
    }
}

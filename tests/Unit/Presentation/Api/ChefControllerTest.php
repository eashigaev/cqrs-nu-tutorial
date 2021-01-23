<?php

namespace Tests\Unit\Presentation\Api;

use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

class ChefControllerTest extends TestCase
{
    public function testCanGetTodoListResponse()
    {
        $queryBus = $this->setUpFakeQueryBus([
            GetTodoList::class => fn() => [1, 2, 3]
        ]);

        $this->get('/api/chef/todo-list')
            ->assertStatus(200)
            ->assertJsonFragment([
                'payload' => [1, 2, 3]
            ]);

        $this->assertHandledQueries($queryBus, [
            GetTodoList::of()
        ]);
    }
}

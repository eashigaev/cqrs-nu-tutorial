<?php

namespace Tests\Unit\Presentation\Api;

use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

class ChefTodoListTest extends TestCase
{
    public function testCanGetTodoListResponse()
    {
        $this->queryBus->subscribe(GetTodoList::class, function ($query) {
            $this->assertEquals(GetTodoList::of(), $query);
            return [1, 2, 3];
        });

        $response = $this->get('/api/chef/todo-list');

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'payload' => [1, 2, 3]
            ]);
    }
}

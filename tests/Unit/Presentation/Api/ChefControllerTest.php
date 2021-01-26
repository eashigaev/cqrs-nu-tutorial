<?php

namespace Tests\Unit\Presentation\Api;

use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

class ChefControllerTest extends TestCase
{
    public function testCanQueryGetTodoList()
    {
        $this
            ->mockQueryBus()
            ->with(GetTodoList::of())
            ->willReturn([1, 2, 3]);

        $this->get('/api/chef/todo-list')
            ->assertStatus(200)
            ->assertJsonFragment([
                'payload' => [1, 2, 3]
            ]);
    }

    public function testCanCommandGetTodoList()
    {
        $this
            ->mockQueryBus()
            ->with(GetTodoList::of())
            ->willReturn([1, 2, 3]);

        $this->get('/api/chef/todo-list')
            ->assertStatus(200)
            ->assertJsonFragment([
                'payload' => [1, 2, 3]
            ]);
    }
}

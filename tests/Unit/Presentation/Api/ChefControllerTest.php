<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Support\Collection;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

class ChefControllerTest extends TestCase
{
    public function testCanGetTodoListResponse()
    {
        $this
            ->setUpMock(ChefTodoListInterface::class)
            ->expects($this->once())
            ->method('getTodoList')
            ->with(GetTodoList::of())
            ->willReturn(Collection::make([1, 2, 3]));

        $this->get('/api/chef/todo-list')
            ->assertStatus(200)
            ->assertJsonFragment([
                'payload' => [1, 2, 3]
            ]);
    }
}

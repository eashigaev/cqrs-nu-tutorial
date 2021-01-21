<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;

class ChefControllerTest extends TestCase
{
    public function testCanGetTodoListResponse()
    {
        $this->queryBus->subscribe(GetTodoList::class, fn() => [1, 2, 3]);

        $this->get('/api/chef/todo-list')
            ->assertStatus(200)
            ->assertJsonFragment([
                'payload' => [1, 2, 3]
            ]);

        $this->assertHandledQueries($this->queryBus, [
            GetTodoList::of()
        ]);
    }

    public function assertHandledQueries(QueryBusInterface $queryBus, array $queries)
    {
        $this->assertEquals($queries, $queryBus->releaseQueries());
    }
}

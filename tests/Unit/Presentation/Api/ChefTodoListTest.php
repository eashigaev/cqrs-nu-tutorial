<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerTestTrait;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Tests\TestCase;

class ChefTodoListTest extends TestCase
{
    use ContainerTestTrait,
        WithoutMiddleware;

    protected ChefTodoListInterface $chefTodoList;

    public function setUp(): void
    {
        parent::setUp();

        $this->chefTodoList = $this->container()->make(ChefTodoListInterface::class);
    }

    public function testCanGetTodoListResponse()
    {
        $this->chefTodoList->mock(GetTodoList::class, function ($query) {
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

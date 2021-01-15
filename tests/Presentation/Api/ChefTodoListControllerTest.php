<?php

namespace Tests\Presentation\Api;

use Tests\TestCase;

class ChefTodoListControllerTest extends TestCase
{
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

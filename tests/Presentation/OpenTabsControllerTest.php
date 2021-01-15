<?php

namespace Tests\Presentation;

use Tests\TestCase;

class OpenTabsControllerTest extends TestCase
{
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

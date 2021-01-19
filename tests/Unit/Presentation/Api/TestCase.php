<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerTestTrait;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase as BaseTestCase;
use Tests\Unit\FixtureTestTrait;

abstract class TestCase extends BaseTestCase
{
    use WithoutMiddleware,
        FixtureTestTrait,
        ContainerTestTrait;
}

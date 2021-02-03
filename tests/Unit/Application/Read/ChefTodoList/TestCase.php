<?php

namespace Tests\Unit\Application\Read\ChefTodoList;

use Codderz\Yoko\Layers\Application\Read\ReadModel\ReadModelTestTrait;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerTestTrait;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Tests\FixtureTestTrait;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations,
        FixtureTestTrait,
        ReadModelTestTrait,
        ContainerTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFixture();
    }

    public function chefTodoList(array $events = []): ChefTodoListInterface
    {
        return $this
            ->container()
            ->make(ChefTodoListInterface::class)
            ->applyAll($events);
    }
}

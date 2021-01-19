<?php

namespace Tests\Unit\Application\Read\OpenTabs;

use Codderz\Yoko\Layers\Application\Read\ReadTestTrait;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerTestTrait;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Tests\TestCase as BaseTestCase;
use Tests\Unit\FixtureTestTrait;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations,
        FixtureTestTrait,
        ReadTestTrait,
        ContainerTestTrait;

    protected OpenTabsInterface $openTabs;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFixture();
        $this->openTabs = $this->container()->make(OpenTabsInterface::class);
    }
}

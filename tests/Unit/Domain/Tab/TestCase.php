<?php

namespace Tests\Unit\Domain\Tab;

use Codderz\Yoko\Layers\Domain\DomainTestTrait;
use Tests\FixtureTestTrait;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DomainTestTrait,
        FixtureTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFixture();
    }
}

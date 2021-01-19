<?php

namespace Tests\Unit\Domain\Tab;

use Codderz\Yoko\Layers\Domain\DomainTestTrait;
use Tests\TestCase as BaseTestCase;
use Tests\Unit\FixtureTestTrait;

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

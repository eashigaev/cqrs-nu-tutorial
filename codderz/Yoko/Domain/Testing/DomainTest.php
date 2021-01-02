<?php

namespace Codderz\Yoko\Domain\Testing;

use Codderz\Yoko\Domain\Aggregate;
use PHPUnit\Framework\Assert;

class DomainTest
{
    protected Aggregate $aggregate;
    protected ?\Exception $exception;

    public static function given(Aggregate $aggregate)
    {
        $self = new self;
        $self->aggregate = $aggregate;
        return $self;
    }

    public function when(array $commands)
    {
        $this->exception = null;
        try {
            foreach ($commands as $command) {
                $this->aggregate->handle($command);
            }
        } catch (\Exception $exception) {
            $this->exception = $exception;
        }
        return $this;
    }

    public function then(array $events)
    {
        if ($this->exception) {
            throw new \Error("Expected events but got exception " . get_class($this->exception));
        }

        Assert::assertEquals($events, $this->aggregate->releaseEvents());
        return $this;
    }

    public function thenFail(\Exception $exception)
    {
        Assert::assertEquals($exception, $this->exception);
        return $this;
    }

    public function with(callable $callable)
    {
        $callable($this->aggregate);
        return $this;
    }
}

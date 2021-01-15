<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Support\Reflect;

class ReadModel implements ReadModelInterface
{
    protected array $mocks = [];

    public function mock(string $query, callable $callback)
    {
        $this->mocks[$query] = $callback;
    }

    public function handle($query)
    {
        $method = lcfirst(Reflect::shortClass($query));

        if (method_exists($this, $method)) {

            $mock = $this->mocks[get_class($query)] ?? null;
            if ($mock) return $mock($query);

            return $this->$method($query);
        }

        throw new \Error(
            get_class($this) . " does not yet handle query " . get_class($query)
        );
    }

    public function apply($event)
    {
        $method = __FUNCTION__ . Reflect::shortClass($event);

        if (method_exists($this, $method)) {
            $this->$method($event);
            return $this;
        };

        throw new \Error(
            get_class($this) . " does not know how to apply event " . get_class($event)
        );
    }

    public function withEvents(array $events = [])
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
        return $this;
    }
}

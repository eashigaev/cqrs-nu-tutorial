<?php

namespace Codderz\Yoko\Layers\Application;

use Codderz\Yoko\Support\Reflect;

class Messenger
{
    protected object $target;

    public static function of(object $target)
    {
        $self = new self();
        $self->target = $target;
        return $self;
    }

    public function handle($message)
    {
        $method = lcfirst(Reflect::shortClass($message));

        if ($this->can($message, $method)) {
            return $this->target->$method($message);
        }

        throw NotHandled::new();
    }

    public function apply($event)
    {
        $method = __FUNCTION__ . Reflect::shortClass($event);

        if ($this->can($event, $method)) {
            $this->target->$method($event);
        }

        return $this;
    }

    public function can($message, $method)
    {
        return method_exists($this->target, $method)
            && Reflect::paramTypes($this->target, $method) === [get_class($message)];
    }
}

<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Factory;

interface HandlerFactoryInterface
{
    public function make($handler, $method);
}

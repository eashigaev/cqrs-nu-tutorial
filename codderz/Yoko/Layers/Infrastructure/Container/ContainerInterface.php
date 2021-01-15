<?php

namespace Codderz\Yoko\Layers\Infrastructure\Container;

interface ContainerInterface
{
    public function make(string $contract);
}

<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging;

interface HandleMessageInterface
{
    public function handle($message);
}

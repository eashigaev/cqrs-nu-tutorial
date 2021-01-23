<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

use Codderz\Yoko\Layers\Application\Read\QueryBus\QuerySubscriberInterface;

interface MessageSubscriberInterface extends QuerySubscriberInterface
{
    public function listen(string $message, $handler);
}

<?php

namespace Codderz\Yoko\Layers\Application\Write\CommandBus;

use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageBus;
use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageResolverInterface;

class CommandBus extends MessageBus implements CommandBusInterface
{
    public function __construct(CommandMapperInterface $mapper, MessageResolverInterface $resolver)
    {
        parent::__construct($mapper, $resolver);
    }
}

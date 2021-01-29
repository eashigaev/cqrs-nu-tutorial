<?php

namespace Codderz\Yoko\Layers\Application\Write\CommandBus;

use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageBus;

class CommandBus extends MessageBus implements CommandBusInterface, CommandResolverInterface
{
}

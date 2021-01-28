<?php

namespace Codderz\Yoko\Layers\Application\Write\CommandBus;

use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageResolverInterface;

class CommandBus implements CommandBusInterface
{
    protected CommandMapperInterface $mapper;
    protected MessageResolverInterface $resolver;

    protected array $queue = [];
    protected bool $isHandling = false;

    public function __construct(CommandMapperInterface $mapper, MessageResolverInterface $resolver)
    {
        $this->mapper = $mapper;
        $this->resolver = $resolver;
    }

    public function handle($message)
    {
        $this->queue[] = $message;

        if (!$this->isHandling) {

            $this->isHandling = true;

            while ($message = array_shift($this->queue)) {
                $handler = $this->mapper->map($message);
                $handler = $this->resolver->resolve($message, $handler);

                $handler($message);
            }

            $this->isHandling = false;
        }
    }
}

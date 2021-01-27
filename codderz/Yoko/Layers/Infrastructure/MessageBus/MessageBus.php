<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

class MessageBus implements MessageBusInterface
{
    protected MessageMapperInterface $mapper;
    protected MessageResolverInterface $resolver;

    public function __construct(MessageMapperInterface $mapper, MessageResolverInterface $resolver)
    {
        $this->mapper = $mapper;
        $this->resolver = $resolver;
    }

    public function handle($message)
    {
        $handler = $this->mapper->map($message);

        $handler = $this->resolver->resolve($message, $handler);

        return $handler($message);
    }
}

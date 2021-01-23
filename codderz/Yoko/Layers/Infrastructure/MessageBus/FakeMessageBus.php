<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

class FakeMessageBus implements MessageBusInterface
{
    protected array $handlers = [];
    protected array $handledMessages = [];

    public static function of(array $handlers)
    {
        $self = new self;
        $self->handlers = $handlers;
        return $self;
    }

    public function handle($message)
    {
        $this->handledMessages[] = $message;

        return $this->handlers[get_class($message)]($message);
    }

    public function releaseHandledMessages(): array
    {
        return $this->handledMessages;
    }
}

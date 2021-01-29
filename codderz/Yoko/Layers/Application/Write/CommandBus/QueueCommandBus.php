<?php

namespace Codderz\Yoko\Layers\Application\Write\CommandBus;

class QueueCommandBus implements CommandBusInterface
{
    protected CommandBusInterface $bus;

    protected array $queue = [];
    protected bool $isHandling = false;

    public function __construct(CommandBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function handle($message)
    {
        $this->queue[] = $message;

        if (!$this->isHandling) {
            $this->isHandling = true;

            while ($message = array_shift($this->queue)) {
                $this->bus->handle($message);
            }

            $this->isHandling = false;
        }
    }
}

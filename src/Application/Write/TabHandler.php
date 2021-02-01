<?php

namespace Src\Application\Write;

use Codderz\Yoko\Layers\Infrastructure\EventBus\EventBusInterface;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\MarkFoodPrepared;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\TabAggregate;
use Src\Domain\Tab\TabRepositoryInterface;

class TabHandler
{
    protected EventBusInterface $eventBus;
    protected TabRepositoryInterface $tabRepository;

    public function __construct(EventBusInterface $eventBus, TabRepositoryInterface $tabRepository)
    {
        $this->eventBus = $eventBus;
        $this->tabRepository = $tabRepository;
    }

    public function openTab(OpenTab $command)
    {
        $tab = TabAggregate::openTab($command);
        $this->tabRepository->save($tab);
        $this->eventBus->publishAll($tab->releaseEvents());
    }

    public function placeOrder(PlaceOrder $command)
    {
        $tab = $this->tabRepository->ofId($command->id);
        $tab->placeOrder($command);
        $this->tabRepository->save($tab);
        $this->eventBus->publishAll($tab->releaseEvents());
    }

    public function markDrinksServed(MarkDrinksServed $command)
    {
        $tab = $this->tabRepository->ofId($command->id);
        $tab->markDrinksServed($command);
        $this->tabRepository->save($tab);
        $this->eventBus->publishAll($tab->releaseEvents());
    }

    public function markFoodPrepared(MarkFoodPrepared $command)
    {
        $tab = $this->tabRepository->ofId($command->id);
        $tab->markFoodPrepared($command);
        $this->tabRepository->save($tab);
        $this->eventBus->publishAll($tab->releaseEvents());
    }

    public function markFoodServed(MarkFoodServed $command)
    {
        $tab = $this->tabRepository->ofId($command->id);
        $tab->markFoodServed($command);
        $this->tabRepository->save($tab);
        $this->eventBus->publishAll($tab->releaseEvents());
    }

    public function closeTab(CloseTab $command)
    {
        $tab = $this->tabRepository->ofId($command->id);
        $tab->closeTab($command);
        $this->tabRepository->save($tab);
        $this->eventBus->publishAll($tab->releaseEvents());
    }
}

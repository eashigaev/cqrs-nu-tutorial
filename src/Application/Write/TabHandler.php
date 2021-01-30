<?php

namespace Src\Application\Write;

use App\Models\Write\TabAggregateModel;
use Codderz\Yoko\Layers\Infrastructure\EventBus\EventBusInterface;
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
        dd(TabAggregateModel::all()->toArray());
        $this->tabRepository->save($tab);
    }
}

<?php

namespace Src\Application\Read;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\HandlerSubscriberInterface;
use Src\Application\Write\TabHandler;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\MarkFoodPrepared;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;

class CommandSubscriber
{
    public static function getHandlers(): array
    {
        return [
            OpenTab::class => TabHandler::class,
            CloseTab::class => TabHandler::class,
            MarkDrinksServed::class => TabHandler::class,
            MarkFoodPrepared::class => TabHandler::class,
            MarkFoodServed::class => TabHandler::class,
            PlaceOrder::class => TabHandler::class,
        ];
    }
}

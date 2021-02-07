<?php

namespace Src\Application;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\HandlerSubscriberInterface;
use Src\Application\Read\ChefTodoList\ChefTodoList;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;

class EventSubscriber
{
    public static function getHandlers(): array
    {
        return [
            TabOpened::class => [
                OpenTabsInterface::class
            ],
            DrinksOrdered::class => [
                OpenTabsInterface::class
            ],
            DrinksServed::class => [
                OpenTabsInterface::class
            ],
            FoodOrdered::class => [
                OpenTabsInterface::class,
                ChefTodoListInterface::class
            ],
            FoodPrepared::class => [
                OpenTabsInterface::class,
                ChefTodoListInterface::class
            ],
            FoodServed::class => [
                OpenTabsInterface::class
            ],
            TabClosed::class => [
                OpenTabsInterface::class
            ]
        ];
    }
}

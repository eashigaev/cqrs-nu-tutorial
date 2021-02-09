<?php

namespace Src\Application;

use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Application\Read\OpenTabs\Queries\GetInvoiceForTable;
use Src\Application\Read\OpenTabs\Queries\GetTabForTable;
use Src\Application\Read\OpenTabs\Queries\GetTodoListForWaiter;
use Src\Application\Write\TabHandler;
use Src\Domain\Tab\Commands\CloseTab;
use Src\Domain\Tab\Commands\MarkDrinksServed;
use Src\Domain\Tab\Commands\MarkFoodPrepared;
use Src\Domain\Tab\Commands\MarkFoodServed;
use Src\Domain\Tab\Commands\OpenTab;
use Src\Domain\Tab\Commands\PlaceOrder;
use Src\Domain\Tab\Events\DrinksOrdered;
use Src\Domain\Tab\Events\DrinksServed;
use Src\Domain\Tab\Events\FoodOrdered;
use Src\Domain\Tab\Events\FoodPrepared;
use Src\Domain\Tab\Events\FoodServed;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;

class ApplicationMap
{
    public static function commands(): array
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

    public static function queries(): array
    {
        return [
            GetTodoList::class => ChefTodoListInterface::class,
            GetActiveTableNumbers::class => OpenTabsInterface::class,
            GetInvoiceForTable::class => OpenTabsInterface::class,
            GetTabForTable::class => OpenTabsInterface::class,
            GetTodoListForWaiter::class => OpenTabsInterface::class
        ];
    }

    public static function events(): array
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

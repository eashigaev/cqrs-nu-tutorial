<?php

namespace Src\Application\Write;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\HandlerSubscriberInterface;
use Src\Application\Read\ChefTodoList\ChefTodoList;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Application\Read\OpenTabs\Queries\GetInvoiceForTable;
use Src\Application\Read\OpenTabs\Queries\GetTabForTable;
use Src\Application\Read\OpenTabs\Queries\GetTodoListForWaiter;

class QuerySubscriber
{
    public static function getHandlers(): array
    {
        return [
            GetTodoList::class => ChefTodoListInterface::class,
            GetActiveTableNumbers::class => OpenTabsInterface::class,
            GetInvoiceForTable::class => OpenTabsInterface::class,
            GetTabForTable::class => OpenTabsInterface::class,
            GetTodoListForWaiter::class => OpenTabsInterface::class
        ];
    }
}

<?php

namespace Src;

use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBus;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusHandler;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusHandlerInterface;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryResolver;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryResolverInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBus;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusHandlerInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandResolver;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandResolverInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\QueueCommandBus;
use Codderz\Yoko\Layers\Infrastructure\Container\Container;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Layers\Infrastructure\EventBus\EventBus;
use Codderz\Yoko\Layers\Infrastructure\EventBus\EventBusInterface;
use Codderz\Yoko\Layers\Infrastructure\EventBus\EventResolverInterface;
use Illuminate\Support\ServiceProvider;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Application\Read\OpenTabs\Queries\GetInvoiceForTable;
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
use Src\Domain\Tab\TabRepositoryInterface;
use Src\Infrastructure\Application\Read\EloquentChefTodoList;
use Src\Infrastructure\Application\Read\EloquentOpenTabs;
use Src\Infrastructure\Application\Write\EloquentTabRepository;

class SrcServiceProvider extends ServiceProvider
{
    public static function providers()
    {
        return [self::class];
    }

    public function register()
    {
        $this->app->singleton(ContainerInterface::class, Container::class);

        $this->app->singleton(EventBus::class);
        $this->app->bind(EventResolverInterface::class, EventBus::class);
        $this->app->bind(EventBusInterface::class, EventBus::class);

        $this->app->singleton(QueryBus::class);
        $this->app->bind(QueryResolverInterface::class, QueryBus::class);
        $this->app->bind(QueryBusInterface::class, QueryBus::class);

        $this->app->singleton(CommandBus::class);
        $this->app->bind(CommandResolverInterface::class, CommandBus::class);
        $this->app->bind(CommandBusInterface::class, QueueCommandBus::class);
        $this->app->when(QueueCommandBus::class)->needs(CommandBusInterface::class)->give(CommandBus::class);

        $this->app->singleton(ChefTodoListInterface::class, EloquentChefTodoList::class);
        $this->app->singleton(OpenTabsInterface::class, EloquentOpenTabs::class);

        $this->app->singleton(TabRepositoryInterface::class, EloquentTabRepository::class);
    }

    public function boot(
        QueryResolverInterface $queryResolver,
        CommandResolverInterface $commandResolver,
        EventResolverInterface $eventResolver
    )
    {
        $queryResolver
            ->bindAll(OpenTabsInterface::class, [
                GetActiveTableNumbers::class,
                GetInvoiceForTable::class
            ]);

        $eventResolver
            ->bindAll(OpenTabsInterface::class, [
                TabOpened::class,
                DrinksOrdered::class,
                FoodOrdered::class,
                DrinksServed::class,
                FoodServed::class,
                FoodPrepared::class,
                TabClosed::class
            ]);

        $commandResolver
            ->bindAll(TabHandler::class, [
                OpenTab::class,
                PlaceOrder::class,
                MarkDrinksServed::class,
                MarkFoodPrepared::class,
                MarkFoodServed::class,
                CloseTab::class
            ]);
    }
}

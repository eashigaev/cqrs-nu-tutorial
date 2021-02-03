<?php

namespace Src;

use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBus;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBus;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandResolverInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\QueueCommandBus;
use Codderz\Yoko\Layers\Infrastructure\Container\Container;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Layers\Infrastructure\Messaging\EventBus\EventBus;
use Codderz\Yoko\Layers\Infrastructure\Messaging\EventBus\EventBusInterface;
use Illuminate\Support\ServiceProvider;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Write\TabHandler;
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
        $this->app->bind(EventBusInterface::class, EventBus::class);

        $this->app->singleton(QueryBus::class);
        $this->app->bind(QueryBusInterface::class, QueryBus::class);

        $this->app->singleton(CommandBus::class);
        $this->app->bind(CommandBusInterface::class, QueueCommandBus::class);
        $this->app->when(QueueCommandBus::class)->needs(CommandBusInterface::class)->give(CommandBus::class);

        $this->app->singleton(ChefTodoListInterface::class, EloquentChefTodoList::class);
        $this->app->singleton(OpenTabsInterface::class, EloquentOpenTabs::class);

        $this->app->singleton(TabRepositoryInterface::class, EloquentTabRepository::class);
    }

    public function boot(EventBus $eventBus, QueryBus $queryBus, CommandBus $commandBus)
    {
        $queryBus
            ->register(OpenTabsInterface::class)
            ->register(ChefTodoListInterface::class);

        $eventBus
            ->register(OpenTabsInterface::class)
            ->register(ChefTodoListInterface::class);

        $commandBus
            ->register(TabHandler::class);
    }
}

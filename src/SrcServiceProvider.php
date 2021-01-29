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
use Illuminate\Support\ServiceProvider;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\ChefTodoList\Queries\GetTodoList;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Infrastructure\Application\Read\EloquentChefTodoList;
use Src\Infrastructure\Application\Read\EloquentOpenTabs;

class SrcServiceProvider extends ServiceProvider
{
    public static function providers()
    {
        return [self::class];
    }

    public function boot(QueryResolverInterface $queryResolver)
    {
        $queryResolver->on(GetTodoList::class, ChefTodoListInterface::class);
    }

    public function register()
    {
        $this->app->singleton(ContainerInterface::class, Container::class);

        $this->app->singleton(QueryBus::class);
        $this->app->bind(QueryResolverInterface::class, QueryBus::class);
        $this->app->bind(QueryBusInterface::class, QueryBus::class);

        $this->app->singleton(CommandBus::class);
        $this->app->bind(CommandResolverInterface::class, CommandBus::class);
        $this->app->bind(CommandBusInterface::class, QueueCommandBus::class);
        $this->app->when(QueueCommandBus::class)->needs(CommandBusInterface::class)->give(CommandBus::class);

        $this->app->singleton(ChefTodoListInterface::class, EloquentChefTodoList::class);
        $this->app->singleton(OpenTabsInterface::class, EloquentOpenTabs::class);
    }
}

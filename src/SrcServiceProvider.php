<?php

namespace Src;

use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBus;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryMapper;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryMapperInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBus;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandMapper;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandMapperInterface;
use Codderz\Yoko\Layers\Infrastructure\Container\Container;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageResolver;
use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageResolverInterface;
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

    public function boot(QueryMapperInterface $querySubscriber)
    {
        $querySubscriber->on(GetTodoList::class, ChefTodoListInterface::class);
    }

    public function register()
    {
        $this->app->singleton(ContainerInterface::class, Container::class);

        $this->app->singleton(MessageResolverInterface::class, MessageResolver::class);

        $this->app->singleton(QueryBusInterface::class, QueryBus::class);
        $this->app->singleton(QueryMapperInterface::class, QueryMapper::class);

        $this->app->singleton(CommandBusInterface::class, CommandBus::class);
        $this->app->singleton(CommandMapperInterface::class, CommandMapper::class);

        $this->app->singleton(ChefTodoListInterface::class, EloquentChefTodoList::class);
        $this->app->singleton(OpenTabsInterface::class, EloquentOpenTabs::class);
    }
}

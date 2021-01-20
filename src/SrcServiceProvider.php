<?php

namespace Src;

use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBus;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
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

    public function boot(QueryBusInterface $queryBus)
    {
        $queryBus->subscribe(GetTodoList::class, ChefTodoListInterface::class);
    }

    public function register()
    {
        $this->app->singleton(ContainerInterface::class, Container::class);

        $this->app->singleton(QueryBusInterface::class, QueryBus::class);

        $this->app->singleton(ChefTodoListInterface::class, EloquentChefTodoList::class);
        $this->app->singleton(OpenTabsInterface::class, EloquentOpenTabs::class);
    }
}

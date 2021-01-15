<?php

namespace Src;

use Codderz\Yoko\Layers\Infrastructure\Container\Container;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Illuminate\Support\ServiceProvider;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Infrastructure\Application\Read\EloquentChefTodoList;
use Src\Infrastructure\Application\Read\EloquentOpenTabs;

class SrcServiceProvider extends ServiceProvider
{
    public static function providers()
    {
        return [
            self::class,
        ];
    }

    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton(ContainerInterface::class, Container::class);

        $this->app->bind(ChefTodoListInterface::class, EloquentChefTodoList::class);
        $this->app->bind(OpenTabsInterface::class, EloquentOpenTabs::class);
    }
}

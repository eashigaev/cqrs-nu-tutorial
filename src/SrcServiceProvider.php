<?php

namespace Src;

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
        $this->app->bind(ChefTodoListInterface::class, EloquentChefTodoList::class);
        $this->app->bind(OpenTabsInterface::class, EloquentOpenTabs::class);
    }
}

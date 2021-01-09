<?php

namespace Src;

use Illuminate\Support\ServiceProvider;
use Src\Application\Read\ChiefTodoList\ChiefTodoListInterface;
use Src\Infrastructure\Application\Read\ChiefTodoList;

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
        $this->app->bind(ChiefTodoListInterface::class, ChiefTodoList::class);
    }
}

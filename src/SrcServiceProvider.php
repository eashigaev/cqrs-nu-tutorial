<?php

namespace Src;

use Codderz\Yoko\Layers\Application\Events\EventEmitter\EventEmitter;
use Codderz\Yoko\Layers\Application\Events\EventEmitter\EventEmitterInterface;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBus;
use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBus;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandResolverInterface;
use Codderz\Yoko\Layers\Application\Write\CommandBus\QueueCommandBus;
use Codderz\Yoko\Layers\Infrastructure\Container\Container;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter\Emitter;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Provider\HandlerProvider;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Provider\HandlerProviderInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Resolver\HandlerResolver;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Resolver\HandlerResolverInterface;
use Illuminate\Support\ServiceProvider;
use Src\Application\EventSubscriber;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\CommandSubscriber;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Write\QuerySubscriber;
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

        $this->app->singleton(HandlerProviderInterface::class, HandlerProvider::class);
        $this->app->singleton(HandlerResolverInterface::class, HandlerResolver::class);

        $this->app->bind(EventEmitterInterface::class, EventEmitter::class);
        $this->app->bind(QueryBusInterface::class, QueryBus::class);
        $this->app->bind(CommandBusInterface::class, CommandBus::class);

        $this->app->singleton(ChefTodoListInterface::class, EloquentChefTodoList::class);
        $this->app->singleton(OpenTabsInterface::class, EloquentOpenTabs::class);

        $this->app->singleton(TabRepositoryInterface::class, EloquentTabRepository::class);
    }

    public function boot(HandlerProviderInterface $handlerProvider)
    {
        $handlerProvider
            ->subscribe(CommandSubscriber::getHandlers())
            ->subscribe(QuerySubscriber::getHandlers())
            ->subscribe(EventSubscriber::getHandlers());
    }
}

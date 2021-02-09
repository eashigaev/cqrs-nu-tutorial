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
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus\Provider\BusMapProvider;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus\Provider\BusProviderInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter\Emitter;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter\Provider\EmitterMapProvider;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter\Provider\EmitterProviderInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Factory\HandlerFactory;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Factory\HandlerFactoryInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Provider\HandlerProvider;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Provider\HandlerProviderInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Provider\HandlerSubscriberInterface;
use Illuminate\Support\ServiceProvider;
use Src\Application\ApplicationMap;
use Src\Application\EventsMap;
use Src\Application\Read\ChefTodoList\ChefTodoListInterface;
use Src\Application\Read\CommandsMap;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Write\QueriesMap;
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

        $this->app->bind(HandlerFactoryInterface::class, HandlerFactory::class);

        $this->app->singleton(BusMapProvider::class);
        $this->app->bind(BusProviderInterface::class, BusMapProvider::class);
        $this->app->bind(QueryBusInterface::class, QueryBus::class);
        $this->app->bind(CommandBusInterface::class, CommandBus::class);

        $this->app->singleton(EmitterMapProvider::class);
        $this->app->bind(EmitterProviderInterface::class, EmitterMapProvider::class);
        $this->app->bind(EventEmitterInterface::class, EventEmitter::class);

        $this->app->singleton(ChefTodoListInterface::class, EloquentChefTodoList::class);
        $this->app->singleton(OpenTabsInterface::class, EloquentOpenTabs::class);

        $this->app->singleton(TabRepositoryInterface::class, EloquentTabRepository::class);
    }

    public function boot(BusMapProvider $busProvider, EmitterMapProvider $emitterProvider)
    {
        $busProvider
            ->map(ApplicationMap::commands())
            ->map(ApplicationMap::queries());

        $emitterProvider
            ->map(ApplicationMap::events());
    }
}

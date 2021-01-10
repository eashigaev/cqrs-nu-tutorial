<?php

namespace Src\Infrastructure\Application\Read;

use App\Models\Read\OpenTabsTabModel;
use Codderz\Yoko\Layers\Application\Read\ReadModel;
use Codderz\Yoko\Support\Collection;
use Src\Application\Read\OpenTabs\OpenTabsInterface;
use Src\Application\Read\OpenTabs\Queries\GetActiveTableNumbers;
use Src\Domain\Tab\Events\TabClosed;
use Src\Domain\Tab\Events\TabOpened;

class EloquentOpenTabs extends ReadModel implements OpenTabsInterface
{
    /* @return Collection<int> */
    public function getActiveTableNumbers(GetActiveTableNumbers $query): Collection
    {
        return OpenTabsTabModel::query()
            ->get()
            ->pipeInto(Collection::class)
            ->map(fn($item) => $item->table_number)
            ->values();
    }

    //

    public function applyTabOpened(TabOpened $event)
    {
        OpenTabsTabModel::query()
            ->insert([
                'tab_id' => $event->id->value,
                'table_number' => $event->tableNumber,
                'waiter' => $event->waiter
            ]);
    }

    public function applyTabClosed(TabClosed $event)
    {
        OpenTabsTabModel::query()
            ->where('tab_id', $event->id->value)
            ->limit(1)
            ->delete();
    }
}

<?php

namespace Src\Infrastructure\Application\Write;

use App\Models\Write\TabAggregateModel;
use Codderz\Yoko\Layers\Domain\Guid;
use Src\Domain\Tab\TabAggregate;
use Src\Domain\Tab\TabRepositoryInterface;

class EloquentTabRepository implements TabRepositoryInterface
{
    public function ofId(Guid $id): ?TabAggregate
    {
        $model = TabAggregateModel::find($id->value);

        $aggregate = json_decode($model->aggregate, true);

        return $model
            ? TabAggregate::fromArray($aggregate)
            : null;
    }

    public function save(TabAggregate $aggregate)
    {
        TabAggregateModel::query()
            ->updateOrInsert([
                'id' => $aggregate->id->value,
                'table' => $aggregate->table,
                'aggregate' => json_encode($aggregate->toArray())
            ]);
    }
}

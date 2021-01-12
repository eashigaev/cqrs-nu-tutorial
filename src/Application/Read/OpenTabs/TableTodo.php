<?php

namespace Src\Application\Read\OpenTabs;

use Codderz\Yoko\Contracts\ArrayableInterface;
use Codderz\Yoko\Support\Collection;

class TableTodo implements ArrayableInterface
{
    public int $tableNumber;
    public string $waiter;
    /** @var Collection<TabItem> */
    public Collection $toServe;
    /** @var Collection<TabItem> */
    public Collection $inPreparation;

    public static function of(
        int $tableNumber, string $waiter, Collection $toServe, Collection $inPreparation
    )
    {
        $self = new self;
        $self->tableNumber = $tableNumber;
        $self->waiter = $waiter;
        $self->toServe = $toServe->assertInstance(TabItem::class);
        $self->inPreparation = $toServe->assertInstance(TabItem::class);
        return $self;
    }

    public function toArray(): array
    {
        return [
            'tableNumber' => $this->tableNumber,
            'waiter' => $this->waiter,
            'toServe' => $this->toServe->toArray(),
            'inPreparation' => $this->toServe->toArray()
        ];
    }
}

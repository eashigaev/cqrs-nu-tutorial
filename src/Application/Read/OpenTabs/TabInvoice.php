<?php

namespace Src\Application\Read\OpenTabs;

use Codderz\Yoko\Contracts\ArrayableInterface;
use Codderz\Yoko\Support\Collection;
use Codderz\Yoko\Support\Guid;

class TabInvoice implements ArrayableInterface
{
    public Guid $tabId;
    public int $tableNumber;
    /** @var Collection<TabItem> */
    public Collection $items;
    public float $total;
    public bool $hasUnservedItems;

    public static function of(
        Guid $tabId, int $tableNumber, Collection $items, float $total, bool $hasUnservedItems
    )
    {
        $self = new self;
        $self->tabId = $tabId;
        $self->tableNumber = $tableNumber;
        $self->items = $items->assertInstance(TabItem::class);
        $self->total = $total;
        $self->hasUnservedItems = $hasUnservedItems;
        return $self;
    }

    public function toArray(): array
    {
        return [
            'tabId' => $this->tabId->value,
            'tableNumber' => $this->tableNumber,
            'items' => $this->items->toArray(),
            'total' => $this->total,
            'hasUnservedItems' => $this->hasUnservedItems
        ];
    }
}

<?php

namespace Src\Domain\Tab;

class OrderedItem
{
    public int $menuNumber;
    public string $description;
    public bool $isDrink;
    public float $price;

    public static function of(int $menuNumber, string $description, bool $isDrink, float $price)
    {
        $self = new static();
        $self->menuNumber = $menuNumber;
        $self->description = $description;
        $self->isDrink = $isDrink;
        $self->price = $price;
        return $self;
    }
}

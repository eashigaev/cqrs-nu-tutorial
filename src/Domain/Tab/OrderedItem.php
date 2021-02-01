<?php

namespace Src\Domain\Tab;

use Codderz\Yoko\Contracts\ArrayableInterface;

class OrderedItem implements ArrayableInterface
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

    public static function fromArray(array $array)
    {
        return self::of(
            $array['menuNumber'],
            $array['description'],
            $array['isDrink'],
            $array['price']
        );
    }

    public function toArray()
    {
        return [
            'menuNumber' => $this->menuNumber,
            'description' => $this->description,
            'isDrink' => $this->isDrink,
            'price' => $this->price
        ];
    }
}

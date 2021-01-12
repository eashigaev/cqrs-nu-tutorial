<?php

namespace Src\Application\Read\OpenTabs;

use Codderz\Yoko\Contracts\ArrayableInterface;

class TabItem implements ArrayableInterface
{
    public int $menuNumber;
    public string $description;
    public float $price;

    public static function of(int $menuNumber, string $description, float $price)
    {
        $self = new self;
        $self->menuNumber = $menuNumber;
        $self->description = $description;
        $self->price = $price;
        return $self;
    }

    public function toArray(): array
    {
        return [
            'menuNumber' => $this->menuNumber,
            'description' => $this->description,
            'price' => $this->price
        ];
    }
}

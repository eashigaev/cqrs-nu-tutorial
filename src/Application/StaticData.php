<?php

namespace Src\Application;

use Codderz\Yoko\Support\Collection;

class StaticData
{
    public static function products(): Collection
    {
        return Collection::of([
            [4, 'Sprite', true, 5.00],
            [10, 'Beer', true, 3.00],
            [16, 'Beef Noodles', false, 10.00],
            [25, 'Vegetable Curry', false, 7.00],
            [34, 'Vegas Steak', false, 5.00]
        ])
            ->map(function ($item) {
                list ($menuNumber, $description, $isDrink, $price) = $item;
                return [
                    'menuNumber' => $menuNumber,
                    'description' => $description,
                    'isDrink' => $isDrink,
                    'price' => $price
                ];
            });
    }
}

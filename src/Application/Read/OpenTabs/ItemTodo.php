<?php

namespace Src\Application\Read\OpenTabs;

use Codderz\Yoko\Contracts\ArrayableInterface;

class ItemTodo implements ArrayableInterface
{
    public int $menuNumber;
    public string $description;

    public static function of(int $menuNumber, string $description)
    {
        $self = new self;
        $self->menuNumber = $menuNumber;
        $self->description = $description;
        return $self;
    }

    public function toArray(): array
    {
        return [
            'menuNumber' => $this->menuNumber,
            'description' => $this->description
        ];
    }
}

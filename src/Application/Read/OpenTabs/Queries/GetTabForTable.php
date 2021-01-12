<?php

namespace Src\Application\Read\OpenTabs\Queries;

class GetTabForTable
{
    public int $table;

    public static function of(int $table)
    {
        $self = new self();
        $self->table = $table;
        return $self;
    }
}

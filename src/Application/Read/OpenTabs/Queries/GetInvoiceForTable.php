<?php

namespace Src\Application\Read\OpenTabs\Queries;

class GetInvoiceForTable
{
    public int $table;

    public static function of(int $table)
    {
        $self = new self();
        $self->table = $table;
        return $self;
    }
}

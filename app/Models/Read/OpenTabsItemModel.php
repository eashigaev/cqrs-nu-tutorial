<?php

namespace App\Models\Read;

use Illuminate\Database\Eloquent\Model;

class OpenTabsItemModel extends Model
{
    const
        TO_SERVE_STATUS = 0,
        IN_PREPARATION_STATUS = 1,
        SERVED_STATUS = 2;

    public $table = 'read_open_tabs_items';
}

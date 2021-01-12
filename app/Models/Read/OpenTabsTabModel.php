<?php

namespace App\Models\Read;

use Illuminate\Database\Eloquent\Model;

class OpenTabsTabModel extends Model
{
    public $table = 'read_open_tabs_tabs';

    public function items()
    {
        return $this->hasMany(OpenTabsItemModel::class, 'tab_id', 'tab_id');
    }
}

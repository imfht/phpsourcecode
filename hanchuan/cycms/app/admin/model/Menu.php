<?php

namespace app\admin\model;

use think\Model;

class Menu extends Model
{
    public function father()
    {
        return $this->hasOne('Menu', 'id', 'pid');
    }
}

<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Menu extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['parent_id', 'order', 'name', 'icon', 'uri', 'permission_name'];

}

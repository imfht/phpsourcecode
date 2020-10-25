<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Image extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

}

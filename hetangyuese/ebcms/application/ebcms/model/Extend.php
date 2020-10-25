<?php
namespace app\ebcms\model;

use think\Model;

class Extend extends Model
{

    protected $pk = 'id';

    public function extendfield()
    {
        return $this->hasMany('Extendfield', 'category_id');
    }
}
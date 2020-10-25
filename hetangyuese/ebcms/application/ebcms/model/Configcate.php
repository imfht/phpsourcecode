<?php
namespace app\ebcms\model;

use think\Model;

class Configcate extends Model
{

    protected $pk = 'id';

    public function config()
    {
        return $this->hasMany('Config', 'category_id');
    }
}
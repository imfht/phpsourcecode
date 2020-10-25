<?php
namespace app\ebcms\model;

use think\Model;

class Form extends Model
{

    protected $pk = 'id';

    public function formfield()
    {
        return $this->hasMany('Formfield', 'category_id');
    }
}
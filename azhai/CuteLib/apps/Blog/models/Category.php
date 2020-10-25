<?php

namespace Blog\Model;

use \Cute\ORM\Model;


/**
* Category 模型
*/
class Category extends Model
{
    use \Blog\Model\CategoryMixin;
    protected $id = NULL;
    public $name = '';

    public static function getTable()
    {
        return 'categories';
    }

    public static function getPKeys()
    {
        return ['id'];
    }

}
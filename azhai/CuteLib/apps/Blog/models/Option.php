<?php

namespace Blog\Model;

use \Cute\ORM\Model;


/**
* Option 模型
*/
class Option extends Model
{
    protected $option_id = NULL;
    public $option_name = '';
    public $option_value = '';
    public $autoload = '';

    public static function getTable()
    {
        return 'options';
    }

    public static function getPKeys()
    {
        return ['option_id'];
    }

    public function getBehaviors()
    {
        return [];
    }
}
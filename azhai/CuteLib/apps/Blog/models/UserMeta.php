<?php

namespace Blog\Model;

use \Cute\ORM\Model;
use \Cute\ORM\Behavior\BelongsTo;


/**
* UserMeta 模型
*/
class UserMeta extends Model
{
    protected $umeta_id = NULL;
    public $user_id = 0;
    public $meta_key = NULL;
    public $meta_value = NULL;

    public static function getTable()
    {
        return 'usermeta';
    }

    public static function getPKeys()
    {
        return ['umeta_id'];
    }

    public function getBehaviors()
    {
        return [
            'user' => new BelongsTo(__NAMESPACE__ . '\\User'),
        ];
    }
}
<?php

namespace Blog\Model;

use \Cute\ORM\Model;
use \Cute\ORM\Behavior\BelongsTo;


/**
* PostMeta 模型
*/
class PostMeta extends Model
{
    protected $meta_id = NULL;
    public $post_id = 0;
    public $meta_key = NULL;
    public $meta_value = NULL;

    public static function getTable()
    {
        return 'postmeta';
    }

    public static function getPKeys()
    {
        return ['meta_id'];
    }

    public function getBehaviors()
    {
        return [
            'post' => new BelongsTo(__NAMESPACE__ . '\\Post'),
        ];
    }
}
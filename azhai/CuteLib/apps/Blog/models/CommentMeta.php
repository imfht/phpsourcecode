<?php

namespace Blog\Model;

use \Cute\ORM\Model;
use \Cute\ORM\Behavior\BelongsTo;


/**
* CommentMeta 模型
*/
class CommentMeta extends Model
{
    protected $meta_id = NULL;
    public $comment_id = 0;
    public $meta_key = NULL;
    public $meta_value = NULL;

    public static function getTable()
    {
        return 'commentmeta';
    }

    public static function getPKeys()
    {
        return ['meta_id'];
    }

    public function getBehaviors()
    {
        return [
            'comment' => new BelongsTo(__NAMESPACE__ . '\\Comment'),
        ];
    }
}
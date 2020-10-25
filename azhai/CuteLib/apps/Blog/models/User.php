<?php

namespace Blog\Model;

use \Cute\ORM\Model;
use \Cute\ORM\Behavior\HasMany;


/**
* User 模型
*/
class User extends Model
{
    use \Blog\Model\UserMixin;
    protected $ID = NULL;
    public $user_nicename = '';
    public $user_email = '';
    public $user_url = '';
    public $user_registered = '0000-00-00 00:00:00';
    public $user_activation_key = '';
    public $user_status = 0;
    public $display_name = '';

    public static function getTable()
    {
        return 'users';
    }

    public static function getPKeys()
    {
        return ['ID'];
    }

    public function getBehaviors()
    {
        return [
            'metas'    => new HasMany(__NAMESPACE__ . '\\UserMeta'),
            'posts'    => new HasMany(__NAMESPACE__ . '\\Post', 'post_author'),
            'comments' => new HasMany(__NAMESPACE__ . '\\Comment'),
            'links'    => new HasMany(__NAMESPACE__ . '\\Link', 'link_owner'),
        ];
    }
}
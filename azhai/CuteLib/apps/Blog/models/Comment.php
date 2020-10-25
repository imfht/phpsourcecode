<?php

namespace Blog\Model;

use \Cute\ORM\Model;
use \Cute\ORM\Behavior\BelongsTo;
use \Cute\ORM\Behavior\HasMany;
use \Cute\ORM\Behavior\ManyToMany;


/**
* Comment 模型
*/
class Comment extends Model
{
    protected $comment_ID = NULL;
    public $comment_post_ID = 0;
    public $comment_author = '';
    public $comment_author_email = '';
    public $comment_author_url = '';
    public $comment_author_IP = '';
    public $comment_date = '0000-00-00 00:00:00';
    public $comment_date_gmt = '0000-00-00 00:00:00';
    public $comment_content = '';
    public $comment_karma = 0;
    public $comment_approved = '';
    public $comment_agent = '';
    public $comment_type = '';
    public $comment_parent = 0;
    public $user_id = 0;

    public static function getTable()
    {
        return 'comments';
    }

    public static function getPKeys()
    {
        return ['comment_ID'];
    }

    public function getBehaviors()
    {
        return [
            'metas'      => new HasMany(__NAMESPACE__ . '\\CommentMeta'),
            'post'       => new BelongsTo(__NAMESPACE__ . '\\Post', 'comment_post_ID'),
            'user'       => new BelongsTo(__NAMESPACE__ . '\\User'),
            'taxonomies' => new ManyToMany(__NAMESPACE__ . '\\TermTaxonomy',
                'object_id', 'term_taxonomy_id', 'term_relationships'),
        ];
    }
}
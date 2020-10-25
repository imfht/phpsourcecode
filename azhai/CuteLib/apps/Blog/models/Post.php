<?php

namespace Blog\Model;

use \Cute\ORM\Model;
use \Cute\ORM\Behavior\BelongsTo;
use \Cute\ORM\Behavior\HasMany;
use \Cute\ORM\Behavior\ManyToMany;


/**
* Post 模型
*/
class Post extends Model
{
    protected $ID = NULL;
    public $post_author = 0;
    public $post_date = '0000-00-00 00:00:00';
    public $post_date_gmt = '0000-00-00 00:00:00';
    public $post_content = '';
    public $post_title = '';
    public $post_excerpt = '';
    public $post_status = '';
    public $comment_status = '';
    public $ping_status = '';
    public $post_password = '';
    public $post_name = '';
    public $to_ping = '';
    public $pinged = '';
    public $post_modified = '0000-00-00 00:00:00';
    public $post_modified_gmt = '0000-00-00 00:00:00';
    public $post_content_filtered = '';
    public $post_parent = 0;
    public $guid = '';
    public $menu_order = 0;
    public $post_type = '';
    public $post_mime_type = '';
    public $comment_count = 0;

    public static function getTable()
    {
        return 'posts';
    }

    public static function getPKeys()
    {
        return ['ID'];
    }

    public function getBehaviors()
    {
        return [
            'metas'      => new HasMany(__NAMESPACE__ . '\\PostMeta'),
            'comments'   => new HasMany(__NAMESPACE__ . '\\Comment', 'comment_post_ID'),
            'author'     => new BelongsTo(__NAMESPACE__ . '\\User', 'post_author'),
            'taxonomies' => new ManyToMany(__NAMESPACE__ . '\\TermTaxonomy', 'object_id',
                'term_taxonomy_id', 'term_relationships'),
        ];
    }
}
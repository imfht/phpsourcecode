<?php

namespace Blog\Model;

use \Cute\ORM\Model;
use \Cute\ORM\Behavior\BelongsTo;


/**
* TermTaxonomy 模型
*/
class TermTaxonomy extends Model
{
    protected $term_taxonomy_id = NULL;
    public $term_id = 0;
    public $taxonomy = '';
    public $description = '';
    public $parent = 0;
    public $count = 0;

    public static function getTable()
    {
        return 'term_taxonomy';
    }

    public static function getPKeys()
    {
        return ['term_taxonomy_id'];
    }

    public function getBehaviors()
    {
        return [
            'term' => new BelongsTo(__NAMESPACE__ . '\\Term'),
        ];
    }
}
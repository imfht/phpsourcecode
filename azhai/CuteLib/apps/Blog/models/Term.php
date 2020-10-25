<?php

namespace Blog\Model;

use \Cute\ORM\Model;
use \Cute\ORM\Behavior\HasOne;


/**
* Term 模型
*/
class Term extends Model
{
    protected $term_id = NULL;
    public $name = '';
    public $slug = '';
    public $term_group = 0;

    public static function getTable()
    {
        return 'terms';
    }

    public static function getPKeys()
    {
        return ['term_id'];
    }

    public function getBehaviors()
    {
        return [
            'taxonomy' => new HasOne(__NAMESPACE__ . '\\TermTaxonomy'),
        ];
    }
}
<?php

namespace Blog\Model;

use \Cute\ORM\Model;


/**
* TermRelationship 模型
*/
class TermRelationship extends Model
{
    protected $object_id = NULL;
    protected $term_taxonomy_id = NULL;
    public $term_order = 0;

    public static function getTable()
    {
        return 'term_relationships';
    }

    public static function getPKeys()
    {
        return ['object_id', 'term_taxonomy_id'];
    }

    public function getBehaviors()
    {
        return [];
    }
}
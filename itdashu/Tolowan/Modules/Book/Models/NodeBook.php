<?php
namespace Modules\Book\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Relation;

class NodeBook extends Model
{
    public $id;
    public $nid;
    public $bid;
    public $pid;
    public $weight;
    public $title;
    public $created;
    public $changed;

    public function initialize()
    {
        $this->belongsTo('nid', '\Modules\Node\Entity\Node', 'id', array(
            'alias' => 'nodeNode',
            'reusable' => true,
            'foreignKey' => array(
                'action' => Relation::ACTION_CASCADE,
            ),
        ));
        $this->belongsTo('bid', '\Modules\Node\Entity\Node', 'id', array(
            'alias' => 'bookNode',
            'reusable' => true,
            'foreignKey' => array(
                'action' => Relation::ACTION_CASCADE,
            ),
        ));
        // Skips only when inserting
    }

    public function beforeValidationOnCreate()
    {
        if (!$this->created) {
            $this->created = time();
        }
        if (!$this->created) {
            $this->changed = time();
        }
    }

    public function beforeValidationOnUpdate()
    {
        if (!$this->created) {
            $this->changed = time();
        }
    }

    public function getChildren()
    {
        $query = array(
            'conditions' => 'pid = :parent:',
            'bind' => array(
                'parent' => $this->id,
            ),
            'order' => 'weight DESC',
        );
        $output = self::find($query);
        return $output;
    }
}

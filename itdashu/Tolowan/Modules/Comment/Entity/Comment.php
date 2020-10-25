<?php
namespace Modules\Comment\Entity;

use Modules\Entity\Entity\EntityModel;
use Phalcon\Mvc\Model\Relation;

/**
 *
 */
class Comment extends EntityModel
{
    protected $_source = 'comment';

    protected $_module = 'comment';

    protected $_entity = 'comment';

    protected $_entityId = 'comment';

    public $created;

    public $changed;

    public function initialize()
    {
        $this->belongsTo('nid', '\Modules\Node\Entity\Node', 'id', array(
            'alias' => 'node',
            'reusable' => true,
            'foreignKey' => array(
                'action' => Relation::ACTION_CASCADE,
            ),
        ));
        $this->belongsTo('uid', '\Modules\User\Entity\User', 'id', array(
            'alias' => 'user',
            'foreignKey' => array(
                'reusable' => true,
                'action' => Relation::ACTION_CASCADE,
            ),
        ));
        parent::initialize();
    }

    public function setCreated($date)
    {
        if (strtotime($date)) {
            $this->created = strtotime($date);
        }
    }

    public function renderCreated()
    {
        return date('Y-m-d H:i:s', $this->created);
    }

    public function setChanged($date)
    {
        if (strtotime($date)) {
            $this->changed = strtotime($date);
        }
    }

    public function renderChanged()
    {
        return date('Y-m-d H:i:s', $this->changed);
    }

    public function beforeValidationOnCreate()
    {
        $this->changed = time();
        $this->created = time();
        if ($this->getDI()->getUser()->isLogin()) {
            $this->uid = $this->getDI()->getUser()->id;
        } else {
            return false;
        }
    }

    public function beforeValidationOnUpdate()
    {
        $this->changed = time();
    }

    public function afterSave()
    {
        global $di;
        $nodeEntity = $di->getShared('entityManager')->get('node');
        $node = $nodeEntity->findFirst($this->nid, true);
        $node->comment_num = $node->comment_num + 1;
        $node->save();
    }
}

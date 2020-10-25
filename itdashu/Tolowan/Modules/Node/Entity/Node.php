<?php
namespace Modules\Node\Entity;

use Modules\Entity\Entity\EntityModel;
use Phalcon\Mvc\Model\Relation;

/**
 *
 */
class Node extends EntityModel
{
    /**
     * @var string
     * 映射数据库表
     */
    protected $_source = 'node';

    /**
     * @var mixed
     */
    protected $created = false;

    /**
     * @var string
     * 当前实体类
     */
    protected $entityClassName = '\Modules\Node\Entity\Node';

    /**
     * @var string
     * 所属模块
     */
    protected $_module = 'node';

    /**
     * @var string
     * 实体机读名
     */
    protected $_entityId = 'node';

    /**
     * @var mixed
     */
    protected $changed = false;

    protected $_isLove = null;

    public $contentToc;

    public function initialize()
    {
        parent::initialize();
        $this->belongsTo('uid', '\Modules\User\Entity\User', 'id', array(
            'alias' => 'user',
            'reusable' => true,
        ));
        // Skips only when inserting
    }

    public function getCreated()
    {
        if ($this->created === false) {
            $this->created = time();
        }
        return $this->created;
    }

    public function beforeValidationOnCreate()
    {
        if (!$this->uid) {
            $this->uid = $this->getDI()->getUser()->id;
        }
        parent::beforeValidationOnCreate();
    }

    /**
     * @param $created
     */
    public function setCreated($created)
    {
        if (strtotime($created)) {
            $created = strtotime($created);
        }
        $this->created = $created;
    }

    public function getChanged()
    {
        if ($this->changed === false) {
            $this->changed = time();
        }
        return $this->changed;
    }

    public function setChanged($changed)
    {
        if (strtotime($changed)) {
            $changed = strtotime($changed);
        }
        $this->changed = $changed;
    }

    /**
     * @return mixed
     */
    public function renderState()
    {
        $output = array(
            '审核中',
            '已发布',
            '回收站',
            '禁用',
        );
        return $output[$this->state];
    }

    public function renderTop()
    {
        if ($this->top) {
            return '置顶';
        }
        return false;
    }

    public function renderEssence()
    {
        if ($this->essence) {
            return '精华';
        }
        return false;
    }

    public function renderHot()
    {
        if ($this->hot) {
            return '热点';
        }
        return false;
    }

    public function isLove()
    {
        if (is_null($this->_isLove) === false) {
            return $this->_isLove;
        }
        if (!$this->getDI()->getUser()->isLogin()) {
            $this->_isLove = false;
            return false;
        }
        $userLog = \Modules\User\Models\UserLog::findFirst(array(
            'conditions' => 'uid = :uid: AND type = :type:',
            'bind' => array(
                'uid' => $this->getDI()->getUser()->id,
                'type' => 'node-love-' . $this->id
            )
        ));
        if ($userLog) {
            $this->_isLove = true;
            return true;
        }
        $this->_isLove = false;
        return false;
    }

}

<?php
namespace Modules\User\Entity;

use Modules\Entity\Entity\EntityModel;

/**
 *
 */
class User extends EntityModel
{
    /**
     * @var string
     * 映射数据库表
     */
    protected $_source = 'user';

    /**
     * @var mixed
     */
    protected $created = false;

    /**
     * @var string
     * 当前实体类
     */
    protected $entityClassName = '\Modules\User\Entity\User';

    /**
     * @var string
     * 所属模块
     */
    protected $_module = 'user';

    /**
     * @var string
     * 实体机读名
     */
    protected $_entityId = 'user';

    /**
     * @var mixed
     */
    protected $changed = false;

    protected $password;

    public function getPassword(){
        return $this->password;
    }

    public function setPassword($password){
        if($this->password != $password){
            $this->password = $this->getDI()->getSecurity()->hash($password);
        }
    }
    public function getCreated()
    {
        if ($this->created === false) {
            $this->created = time();
        }
        return date('Y-m-d H:i:s', $this->created);
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

    public function links()
    {
        $this->_links = array(
            'edit' => array(
                'href' => array(
                    'for' => 'adminEntityEdit',
                    'entity' => $this->_entityId,
                    'contentModel' => isset($this->contentModel) && $this->contentModel ? $this->contentModel : 'user',
                    'id' => $this->id,
                ),
                'icon' => 'info',
                'name' => '编辑',
            ),
            'delete' => array(
                'href' => array(
                    'for' => 'adminEntityDelete',
                    'entity' => $this->_entityId,
                    'id' => $this->id,
                ),
                'icon' => 'danger',
                'name' => '删除',
            ),
        );
        if ($this->getDI()->getEventsManager()->fire('entity:links', $this) === false) {
            return false;
        }
        return $this->_links;
    }

    public function getChanged()
    {
        if ($this->changed === false) {
            $this->changed = time();
        }
        return date('Y-m-d H:i:s', $this->changed);
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

}

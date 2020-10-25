<?php

namespace App\Model;

/**
 * 微信用户模型.
 */
class WxUserGroup extends \App\Component\BaseModel
{
    public $primary = 'groupId';
    /**
     * 表名.
     *
     * @var string
     */
    public $table = 'wx_user_group';
    /**
     * 获取所有用户组列表
     * @return array
     */
    public function getUserGroupList()
    {
        $groupList = $this->gets([
            'select' => 'groupId,wxGroupId,groupName,userCount,parentId,orderNum',
            'from' => $this->table,
            'where' => "isDel=0",
            'order' => "orderNum ASC,groupId ASC",
        ]);
        return $groupList;
    }
}

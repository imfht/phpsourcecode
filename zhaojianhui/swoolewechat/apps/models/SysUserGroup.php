<?php
namespace App\Model;
/**
 * 系统用户分组表模型
 * @package App\Model
 */
class SysUserGroup extends \App\Component\BaseModel
{
    public $primary = 'groupId';
    /**
     * 表名
     * @var string
     */
    public $table = 'sys_user_group';

    /**
     * 获取所有用户组列表
     * @return array
     */
    public function getUserGroupList()
    {
        $groupList = $this->gets([
            'select' => 'groupId,groupName,parentId,orderNum,ruleIds',
            'from' => $this->table,
            'where' => "isDel=0",
            'order' => "orderNum ASC,groupId ASC",
        ]);
        return $groupList;
    }
    /**
     * 获取用户的分组列表
     * @param $userId
     * @return array
     */
    public function getUserGroupListByUid($userId)
    {
        $groupList = $this->gets([
            'select' => 'sys_user_group.groupId,sys_user_group.groupName,sys_user_group.ruleIds',
            'from' => $this->table,
            'join' => ['sys_user_to_group', 'sys_user_to_group.userId='.$userId.' AND sys_user_group.groupId=sys_user_to_group.groupId'],
            'where' => "sys_user_group.isDel=0"
        ]);
        return $groupList;
    }
}
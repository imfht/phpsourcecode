<?php
namespace App\Model;
/**
 * 系统用户表模型
 * @package App\Model
 */
class SysUser extends \App\Component\BaseModel
{
    public $primary = 'id';
    /**
     * 表名
     * @var string
     */
    public $table = 'sys_user';

    /**
     * 查询用户列表
     * @return mixed
     */
    public function getUserList($params = [])
    {
        $params['leftjoin'] = ['sys_user_group', "{$this->table}.groupId = sys_user_group.groupId"];

        $list = $this->gets($params);
        return $list;
    }
}
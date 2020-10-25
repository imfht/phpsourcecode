<?php
namespace App\Model;
/**
 * 系统用户关联分组表模型
 * @package App\Model
 */
class SysUserToGroup extends \App\Component\BaseModel
{
    public $primary = 'id';
    /**
     * 表名
     * @var string
     */
    public $table = 'sys_user_to_group';
}
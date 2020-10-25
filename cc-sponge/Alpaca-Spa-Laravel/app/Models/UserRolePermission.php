<?php
namespace App\Models;

use App\Models\Base\BaseModel;

/**
 *
 * @author ChengCheng
 * @date 2017-04-12 09:40:38
 * @property int(11)     id 'id''
 * @property int(11)     role_id '分组ID''
 * @property int(11)     permission_id '权限ID''
 * @property tinyint(1)  status '状态 1为正常 0为冻结''
 * @property tinyint(2)  is_del '0可用 1不可用''
 * @property datetime    update_time '更新时间''
 * @property datetime    audit_time '审核时间''
 * @property datetime    create_time '创建时间''
 * @property datetime    delete_time '删除时间''
 * @property varchar(64) creator '创建人信息''
 * @property varchar(64) updater '更新人信息''
 * @property varchar(16) ip '加入IP''
 * @property tinyint(4)  level '排序字段 0-9权值''
 */
class UserRolePermission extends BaseModel
{
    // 重定向数据表名字
    protected $table = "tb_user_role_permission";
} 

<?php
namespace App\Models;

use App\Models\Base\BaseModel;

/**
 *
 * @author ChengCheng
 * @date 2017-04-12 15:33:33
 * @property int(11)     id '用户分组id''
 * @property int(11)     member_id '用户ID''
 * @property int(11)     role_id '用户组ID''
 * @property tinyint(2)  role '角色, 默认0-普通，1-组管理员''
 * @property tinyint(1)  status '状态 1为正常 0为冻结''
 * @property tinyint(2)  is_del '0可用 1不可用''
 * @property datetime    update_time '更新时间''
 * @property datetime    audit_time '审核时间''
 * @property datetime    create_time '创建时间''
 * @property datetime    delete_time '删除时间''
 * @property varchar(64) creator '创建人id''
 * @property varchar(64) updater '更新人id''
 * @property varchar(16) ip '加入IP''
 * @property tinyint(4)  level '排序字段 0-9权值''
 */
class UserMemberRole extends BaseModel
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'tb_user_member_role';

    /**
     * 角色限制的部门
     */
    public function department()
    {
        return $this->belongsToMany(UserDepartment::class,UserMemberRoleDepartment::model()->getTable(),'user_member_role_id','user_department_id')->whereNull(UserMemberRoleDepartment::model()->getTable().'.delete_at');
    }

    /**
     * 角色限制的部门
     */
    public function city()
    {
        return $this->hasMany(UserMemberRoleCity::class,'user_member_role_id','id')->whereNull(UserMemberRoleCity::model()->getTable().'.delete_at');
    }

    /**
     * 角色
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class,'role_id','id')->whereNull(UserRole::model()->getTable().'.delete_at');
    }

    /**
     * 角色
     */
    public function member()
    {
        return $this->belongsTo(UserMember::class,'member_id','id')->whereNull(UserMember::model()->getTable().'.delete_at');
    }


}

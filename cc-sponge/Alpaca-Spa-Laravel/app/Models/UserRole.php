<?php
namespace App\Models;

use App\Models\Base\BaseModel;

/**
 *
 * @author ChengCheng
 * @date 2017-04-12 09:41:46
 * @property int(11)      id '分组id''
 * @property varchar(50)  name '分组名称''
 * @property varchar(255) desc '分组描述''
 * @property tinyint(2)   is_del '0可用 1不可用''
 * @property datetime     update_time '更新时间''
 * @property datetime     audit_time '审核时间''
 * @property datetime     create_time '创建时间''
 * @property datetime     delete_time '删除时间''
 * @property varchar(64)  creator '创建人信息''
 * @property varchar(64)  updater '更新人信息''
 * @property varchar(16)  ip '加入IP''
 * @property tinyint(4)   level '排序字段 0-9权值''
 */
class UserRole extends BaseModel
{
    // 重定向数据表名字
    protected $table = "tb_user_role";

    const ROLE_ADMIN = 1;    //管理分组Id， Id =1

    /**
     * 用户所有权限
     */
    public function permission()
    {
        return $this->belongsToMany(UserPermission::class, UserRolePermission::model()->getTable(), 'role_id', 'permission_id')->whereNull(UserRolePermission::model()->getTable() . '.delete_at');
    }

    /**
     * 分页查询
     * @author ChengCheng
     * @date 2016年10月20日 16:12:06
     * @param string $data
     * @return array
     */
    public function lists($data)
    {
        //查询条件
        $query = $this;

        //根据id查询
        if (isset($data['id'])) {
            $query = $query->where('id', $data['id']);
        }
        if (isset($data['key'])) {
            $query = $query->where('name', 'like', "%" . $data['key'] . "%");
        }
        if (isset($data['is_no_admin'])) {
            $query = $query->where('id', '<>', "1");
        }

        //总数
        $total = $query->count();

        //分页参数
        $query = $this->initPaged($query, $data);

        //排序参数
        $query = $this->initOrdered($query, $data);

        //分页查找
        $info = $query->with('permission')->get();

        //返回结果，查找数据列表，总数
        $result          = array();
        $result['list']  = $info->toArray();
        $result['total'] = $total;
        return $result;
    }

    /**
     * 编辑
     * @author ChengCheng
     * @date 2018-04-10 14:08:54
     * @param array $data
     * @return array
     */
    public function edit($data)
    {
        // 判断是否是修改
        if (empty($data['id'])) {
            $model = new self;
        } else {
            $model = self::model()->find($data['id']);
            if (empty($model)) {
                return null;
            }
        }

        if (isset($data['name'])) {
            $model->name = $data['name'];
        }
        if (isset($data['desc'])) {
            $model->desc = $data['desc'];
        }

        // 保存信息
        $model->save();

        //保存权限角色
        if (isset($data['permissions'])) {
            $model->permission()->sync($data['permissions']);
        }

        // 返回结果
        return $model;
    }

} 

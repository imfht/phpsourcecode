<?php
namespace App\Models;

use App\Common\Code;
use App\Common\Msg;
use App\Common\Visitor;
use App\Models\Base\BaseModel;

/**
 *
 * @author ChengCheng
 * @date 2018-10-14 16:14:08
 * @property int(11)      id '用户id''
 * @property varchar(50)  name '用户名称''
 * @property varchar(100) email '邮箱''
 * @property varchar(15)  mobile '手机号''
 * @property tinyint(1)   gender '性别 0不明 1男 2女''
 * @property char(255)    avatar '头像URL''
 * @property varchar(30)  country '用户所在国家''
 * @property varchar(30)  province '用户所在省份''
 * @property varchar(30)  city '用户所在城市''
 * @property varchar(30)  language '语言en,zh_CN,zh_TW''
 * @property varchar(100) passwd '密码''
 * @property varchar(100) open_id '小程序openid''
 * @property int(11)      login_times '登录次数''
 * @property timestamp    login_time '当前登录时间''
 * @property varchar(20)  login_ip '登录ip''
 * @property timestamp    last_login_time '上次登录时间''
 * @property varchar(20)  last_login_ip '上次登录ip''
 */
class UserMember extends BaseModel
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'tb_user_member';

    /**
     * 内置管理员 ID
     */
    const MEMBER_ADMIN = 1;

    /**
     * 用户所有角色 - 对应权限
     */
    public function role()
    {
        return $this->belongsToMany(UserRole::class, UserMemberRole::model()->getTable(), 'member_id', 'role_id')->whereNull(UserMemberRole::model()->getTable() . '.delete_at');
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

        //根据key模糊查询name等
        if (isset($data['key'])) {
            $query = $query->where('name', 'like', "%" . $data['key'] . "%");
        }
        //根据id查询
        if (isset($data['id'])) {
            $query = $query->where('id', $data['id']);
        }

        //关联信息
        $with         = [];
        $with['role'] = function () {
        };

        //总数
        $total = $query->count();

        //分页参数
        $query = $this->initPaged($query, $data);

        //排序参数
        $query = $this->initOrdered($query, $data);

        //关联信息
        $query = $query->with($with);

        //分页查找
        $info = $query->get()->makeHidden('passwd');

        $info = $info->toArray();

        //返回结果，查找数据列表，总数
        $result          = array();
        $result['list']  = $info;
        $result['total'] = $total;

        return $result;
    }

    /**
     * 登录,并且返回用户信息
     * @author ChengCheng
     * @date 2016年10月20日 16:12:06
     * @param string $memberId
     * @return array
     */
    public function login($memberId = null)
    {
        $model = $this;
        // 是否指定了memberId
        if (!empty($memberId)) {
            $model = self::findById($memberId);
        }

        // $model为空
        if (!$model) {
            $result         = [];
            $result['code'] = Code::SYSTEM_ERROR;
            $result['msg']  = Msg::SYSTEM_ERROR;
            return $result;
        }

        // 记录登录信息
        $model->login_times     = $model->login_times + 1;      // 登录次数+1
        $model->last_login_time = $model->login_time;           // 上次登录时间
        $model->last_login_ip   = $model->login_ip;             // 登录IP
        $model->login_time      = Visitor::user()->time;       // 登录时间
        $model->login_ip        = Visitor::user()->ip;         // 登录IP

        // 保存用户信息
        $model->save();

        // 获取用户信息
        $info = $model->info();

        // 返回结果
        $result         = [];
        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Msg::SYSTEM_OK;
        $result['data'] = $info;
        return $result;
    }

    /**
     * 获取用户信息
     * @author ChengCheng
     * @date 2016年10月20日 16:12:06
     * @param string $memberId
     * @return array
     */
    public function info($memberId = null)
    {
        // 是否指定了memberId
        if (!empty($memberId)) {
            $this->id = $memberId;
        }

        // 关联分组查询member信息,auth信息
        $with                    = [];
        $with['role.permission'] = function () {
        };

        $member = self::model()->with($with)->where('id', $this->id)->first()->makeHidden('passwd')->toArray();

        //判断是否是管理员
        if ($this->id == self::MEMBER_ADMIN) {
            $member['isAdmin'] = true;
        } else {
            foreach ($member['role'] as $role) {
                if ($role['id'] == UserRole::ROLE_ADMIN) {
                    $member['isAdmin'] = true;
                }
            }
        }

        // 根据分组获取权限信息,合并权限
        $permission     = [];
        $rolePermission = array_column($member['role'], 'permission');
        foreach ($rolePermission as $value) {
            foreach ($value as $permissionValue) {
                unset($permissionValue['create_at']);
                unset($permissionValue['creator']);
                unset($permissionValue['update_at']);
                unset($permissionValue['updater']);
                unset($permissionValue['ip']);
                unset($permissionValue['delete_at']);
                unset($permissionValue['pivot']);
                $permission[$permissionValue['id']] = $permissionValue;
            }
        }
        $member['permission'] = $permission;

        // 返回结果
        return $member;
    }

    /**
     * 编辑
     * @author ChengCheng
     * @date 2018-04-10 14:08:54
     * @param array $data
     * @return static
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
        // 填充字段
        if (isset($data['name'])) {
            $model->name = $data['name'];
        }
        if (isset($data['email'])) {
            $model->email = $data['email'];
        }
        if (isset($data['mobile'])) {
            $model->mobile = $data['mobile'];
        }
        if (isset($data['gender'])) {
            $model->gender = $data['gender'];
        }
        if (isset($data['avatar'])) {
            $model->avatar = $data['avatar'];
        }
        if (isset($data['country'])) {
            $model->country = $data['country'];
        }
        if (isset($data['province'])) {
            $model->province = $data['province'];
        }
        if (isset($data['city'])) {
            $model->city = $data['city'];
        }
        if (isset($data['language'])) {
            $model->language = $data['language'];
        }
        if (isset($data['open_id'])) {
            $model->open_id = $data['open_id'];
        }
        if (!empty($data['passwd'])) {
            $model->passwd = password_hash($data['passwd'], PASSWORD_DEFAULT);
        }

        // 保存信息
        $model->save();

        //保存权限角色
        if (isset($data['roles'])) {
            $model->role()->sync($data['roles']);
        }

        // 返回结果
        return $model;
    }
}
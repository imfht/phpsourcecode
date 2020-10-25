<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Main\Base\BaseController;
use App\Common\Code;
use App\Common\Msg;
use Lib\Validate;
use App\Models\UserRole;
use App\Models\UserPermission;
use App\Models\UserMember;

/**
 * 用户
 * @author Chengcheng
 * @date 2016-10-19 15:50:00
 */
class UserController extends BaseController
{

    /**
     * 用户 - 列表
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function listsMember()
    {
        /*
         * 1 获取输入参数
         * pageNum              页码
         * pageSize             页面大小
         * orderBy              排序字段
         * orderDir             排序方向
         * id                   用户ID
         * user_department_id   部门Id(可选-查询条件)
         * is_fetch_child       是否递归部门(可选-查询条件)
         * */
        $this->requestData['pageNum']            = $this->input('pageNum', '1');
        $this->requestData['pageSize']           = $this->input('pageSize', '20');
        $this->requestData['orders']             = $this->input('orders', null);
        $this->requestData['id']                 = $this->input('id', null);
        $this->requestData['user_department_id'] = $this->input('user_department_id', null);
        $this->requestData['is_fetch_child']     = $this->input('is_fetch_child', 0);
        $this->requestData['key']                = $this->input('key', null);
        $this->requestData['city']               = $this->input('city', null);
        $this->requestData['name']               = $this->input('name', null);
        $this->requestData['gender']             = $this->input('gender', null);
        $this->requestData['staff_type']         = $this->input('staff_type', null);
        $this->requestData['staff_status']       = $this->input('staff_status', null);
        $this->requestData['join_date_begin']    = $this->input('join_date_begin', null);
        $this->requestData['join_date_end']      = $this->input('join_date_end', null);


        //2.1 查找用户信息
        $data = UserMember::model()->lists($this->requestData);

        //3 返回结果
        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Msg::SYSTEM_OK;
        $result['data'] = $data;

        //4 返回结果
        return $this->ajaxReturn($result);
    }

    /**
     * 用户 - 编辑
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function editMember()
    {
        /*
         * 1 获取输入参数
         * name             名字
         * passwd           密码
         * email            邮箱
         * mobile           手机
         * groups           分组Id数组
         * id               id
         * */
        $this->requestData['name']   = $this->input('name', '未命名');
        $this->requestData['passwd'] = $this->input('passwd', null);
        $this->requestData['email']  = $this->input('email', null);
        $this->requestData['mobile'] = $this->input('mobile', null);
        $this->requestData['roles']  = $this->input('roles', null);
        $this->requestData['id']     = $this->input('id', null);

        //2 检查参数
        if (empty($this->requestData['passwd']) && empty($this->requestData['id'])) {
            //新增状态验证passwd是否为空
            $result['code'] = Code::SYSTEM_PARAMETER_NULL;
            $result['msg']  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'passwd');
            return $this->ajaxReturn($result);
        }
        if (empty($this->requestData['email'])) {
            $result['code'] = Code::SYSTEM_PARAMETER_NULL;
            $result['msg']  = sprintf(Msg::SYSTEM_PARAMETER_NULL, 'email');
            return $this->ajaxReturn($result);
        }

        if (!empty($this->requestData['roles']) && !is_array($this->requestData['roles'])) {
            $result['code'] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result['msg']  = sprintf(Msg::SYSTEM_PARAMETER_FORMAT_ERROR, 'roles');
            return $this->ajaxReturn($result);
        }
        if (!Validate::isEmail($this->requestData['email'])) {
            $result['code'] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result['msg']  = sprintf(Msg::SYSTEM_PARAMETER_FORMAT_ERROR, 'email');
            return $this->ajaxReturn($result);
        }

        //格式化groups
        if (empty($this->requestData['roles'])) {
            $this->requestData['roles'] = [];
        }


        //1 判读email是否已经使用
        $memberExist = UserMember::model()->where('email', $this->requestData['email'])->first();

        if (empty($this->requestData['id']) && !empty($memberExist)) {
            //添加状态，id没有指定, 判断FEmail是否存在
            $result["code"] = Code::USER_EMAIL_EXIT;     //Email存在
            $result["msg"]  = Msg::USER_EMAIL_EXIT;
            return $this->ajaxReturn($result);

        } elseif (!empty($memberExist) && $memberExist->id != $this->requestData['id']) {
            //编辑状态状态，id没有指定, 判断FEmail是否存在
            $result["code"] = Code::USER_EMAIL_EXIT;     //Email存在
            $result["msg"]  = Msg::USER_EMAIL_EXIT;
            return $this->ajaxReturn($result);
        }

        //2.1 编辑用户
        $data = UserMember::model()->edit($this->requestData);

        //3 返回结果
        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Msg::SYSTEM_OK;
        $result['data'] = $data;
        return $this->ajaxReturn($result);
    }

    /**
     * 用户 - 删除
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function deleteMember()
    {
        //1 获取输入参数 id
        $this->requestData['id'] = $this->input('id', null);

        //2 检查参数
        if (empty($this->requestData['id'])) {
            $result['code'] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result['msg']  = sprintf(Msg::SYSTEM_PARAMETER_FORMAT_ERROR, 'id');
            return $this->ajaxReturn($result);
        }

        //3 id =1是内置管理员，不可以删除
        if ($this->requestData['id'] == 1) {
            $result['code'] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result['msg']  = "内置默认管理员不可以被删除！";
            return $this->ajaxReturn($result);
        }

        //4 删除
        $data           = UserMember::model()->where('id', $this->requestData['id'])->delete();
        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Msg::SYSTEM_OK;
        $result['data'] = $data;

        //5 返回结果
        return $this->ajaxReturn($result);
    }

    /**
     * 分组 - 列表
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function listsRole()
    {
        /*
         * 1 获取输入参数
         * pageNum              页码
         * pageSize             页面大小
         * orders               排序字段
         * id                   分组ID
         * */
        $this->requestData['pageNum']  = $this->input('pageNum', '1');
        $this->requestData['pageSize'] = $this->input('pageSize', '20');
        $this->requestData['orders']   = $this->input('orders', null);
        $this->requestData['id']       = $this->input('id', null);
        $this->requestData['key']      = $this->input('key', null);

        //2 查找用户信息
        $data = UserRole::model()->lists($this->requestData);

        //3 设置返回结果
        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Msg::SYSTEM_OK;
        $result['data'] = $data;

        //4 返回结果
        return $this->ajaxReturn($result);
    }

    /**
     * 分组 - 编辑
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function editRole()
    {
        //1 获取输入参数,name 分组名称，desc 分组描述, powers 权限id数组
        $this->requestData['name']        = $this->input('name', null);
        $this->requestData['desc']        = $this->input('desc', null);
        $this->requestData['permissions'] = $this->input('permissions', null);
        $this->requestData['id']          = $this->input('id', null);

        //2.1 验证FName，FDesc是否为空
        if (empty($this->requestData['name'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, '姓名');
            return $this->ajaxReturn($result);
        }

        //2.2 验证手机号码是否为空
        if (empty($this->requestData['desc'])) {
            $result["code"] = Code::SYSTEM_PARAMETER_NULL;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_NULL, '描述');
            return $this->ajaxReturn($result);
        }

        //2.3 权限id数组格式
        if (!is_array($this->requestData['permissions']) && $this->requestData['permissions'] != null) {
            $result["code"] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result["msg"]  = sprintf(Msg::SYSTEM_PARAMETER_FORMAT_ERROR, 'permissions');
            return $this->ajaxReturn($result);
        }

        //格式化groups
        if (empty($this->requestData['permissions'])) {
            $this->requestData['permissions'] = [];
        }

        //3 添加分组
        $data = UserRole::model()->edit($this->requestData);

        //5 返回结果
        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Msg::SYSTEM_OK;
        $result['data'] = $data;
        return $this->ajaxReturn($result);
    }

    /**
     * 分组 - 删除
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function deleteRole()
    {
        //1 获取输入参数 id
        $this->requestData['id'] = $this->input('id', null);

        //2 检查参数
        if (empty($this->requestData['id'])) {
            $result['code'] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result['msg']  = sprintf(Msg::SYSTEM_PARAMETER_FORMAT_ERROR, 'id');
            return $this->ajaxReturn($result);
        }

        //3 id =1是内置管理员，不可以删除
        if ($this->requestData['id'] == 1) {
            $result['code'] = Code::SYSTEM_PARAMETER_FORMAT_ERROR;
            $result['msg']  = "内置默认管理员分组不可以被删除！";
            return $this->ajaxReturn($result);
        }

        //4 删除
        $data           = UserRole::model()->where('id', $this->requestData['id'])->delete();
        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Msg::SYSTEM_OK;
        $result['data'] = $data;

        //5 返回结果
        return $this->ajaxReturn($result);
    }

    /**
     * 权限 - 列表
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    public function listsPermission()
    {
        /*
         * 1 获取输入参数
         * pageNum              页码
         * pageSize             页面大小
         * orderBy              排序字段
         * orderDir             排序方向
         * id                   分组ID
         * */
        $this->requestData['pageNum']  = $this->input('pageNum', '1');
        $this->requestData['pageSize'] = $this->input('pageSize', '20');
        $this->requestData['orders']   = $this->input('orders', null);
        $this->requestData['id']       = $this->input('id', null);

        //2 查找信息
        $data = UserPermission::model()->lists($this->requestData);

        //3 设置返回结果
        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Msg::SYSTEM_OK;
        $result['data'] = $data;

        //4 返回结果
        return $this->ajaxReturn($result);
    }
}
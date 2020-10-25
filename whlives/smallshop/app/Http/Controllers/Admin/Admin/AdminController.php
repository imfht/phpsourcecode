<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;

class AdminController extends BaseController
{

    /**
     * 列表获取
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        list($page, $limit, $offset) = get_page_params();
        //搜索
        $where = array();
        $username = $request->input('username');
        if ($username) $where[] = array('username', 'like', '%' . $username . '%');
        $res_list = Admin::select('id', 'username', 'tel', 'email', 'status', 'created_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $total = Admin::where($where)->count();
        return $this->success($res_list, $total);
    }

    /**
     * 当前用户信息
     * @param Request $request
     * @return array
     */
    public function info(Request $request)
    {
        $user_data = $this->getUserInfo();
        return $this->success($user_data);
    }

    /**
     * 修改当前用户信息
     * @param Request $request
     * @return array
     */
    public function infoUpdate(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'tel' => 'required',
            'email' => 'required|email',
        ], [
            'tel.required' => '电话不能为空',
            'email.required' => '邮箱地址不能为空',
            'email.email' => '邮箱格式错误'
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['tel', 'email']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $password = $request->input('password');
        if ($password) {
            $save_data['password'] = Hash::make($password);
        }

        $id = $this->getUserId();
        if ($id) {
            $res = Admin::where('id', $id)->update($save_data);
        }
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }

    /**
     * 根据id获取信息
     * @param Request $request
     * @return array
     */
    public function detail(Request $request)
    {
        $id = (int)$request->input('id');
        if ($id) {
            $data = Admin::find($id);
        }
        if (!$data) {
            api_error(__('admin.content_is_empty'));
        }
        return $this->success($data);
    }

    /**
     * 添加编辑
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function save(Request $request)
    {
        $id = (int)$request->input('id');
        //验证规则
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                Rule::unique('admin')->ignore($request->id)
            ],
            'role_id' => 'required|numeric',
            'tel' => 'required',
            'email' => 'required|email',
        ], [
            'username.required' => '用户名不能为空',
            'username.unique' => '用户已经存在',
            'role_id.required' => '角色不能为空',
            'role_id.numeric' => '角色只能是数字',
            'tel.required' => '电话不能为空',
            'email.required' => '邮箱地址不能为空',
            'email.email' => '邮箱格式错误'
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['username', 'role_id', 'tel', 'email']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $password = $request->input('password');
        if ($password) {
            $save_data['password'] = Hash::make($password);
        }

        if ($id) {
            $res = Admin::where('id', $id)->update($save_data);
        } else {
            if (!$password) {
                api_error(__('admin.admin_password_empty'));
            }
            $result = Admin::create($save_data);
            $res = $result->id;
        }
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }

    /**
     * 修改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $ids = $this->checkBatchId();
        $status = (int)$request->input('status');
        if ($ids && isset($status)) {
            $res = Admin::whereIn('id', $ids)->update(['status' => $status]);
            if ($res) {
                return $this->success();
            } else {
                api_error(__('admin.fail'));
            }
        } else {
            api_error(__('admin.invalid_params'));
        }
    }

    /**
     * 删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $ids = $this->checkBatchId();
        $res = Admin::whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }
}

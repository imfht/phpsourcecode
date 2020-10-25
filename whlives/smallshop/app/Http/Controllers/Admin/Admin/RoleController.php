<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class RoleController extends BaseController
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
        $title = $request->input('title');
        if ($title) $where[] = array('title', 'like', '%' . $title . '%');
        $res_list = AdminRole::select('id', 'title', 'created_at', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $total = AdminRole::where($where)->count();
        return $this->success($res_list, $total);
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
            $data = AdminRole::find($id);
            $data['right'] = json_decode($data['right'], true);
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
            'title' => [
                'required',
                Rule::unique('admin_role')->ignore($id)
            ],
            'right' => 'required',
        ], [
            'title.required' => '角色名称不能为空',
            'title.unique' => '角色名称已经存在',
            'right.required' => '权限不能为空'
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['title']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }
        $right = $request->input('right');
        foreach ($right as $key => $value) {
            if ($value) {
                foreach ($value as $k => $v) {
                    $right[$key][$k] = array_values($v);
                }
            }
        }
        $save_data['right'] = json_encode($right);

        if ($id) {
            $res = AdminRole::where('id', $id)->update($save_data);
            AdminRole::adminRight($id, true);//刷新缓存
        } else {
            $result = AdminRole::create($save_data);
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
            $res = AdminRole::whereIn('id', $ids)->update(['status' => $status]);
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
        if (in_array(1, $ids)) {
            api_error(__('admin.admin_role_no_del'));
        }
        $res = AdminRole::whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 获取下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        //角色
        $role = AdminRole::where('status', AdminRole::STATUS_ON)
            ->pluck('title', 'id');
        return $this->success($role);
    }
}

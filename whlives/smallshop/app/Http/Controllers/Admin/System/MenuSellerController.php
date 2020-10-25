<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Models\AdminRole;
use App\Models\MenuSeller;
use Illuminate\Http\Request;
use Validator;

/**
 * 商家菜单管理
 * Class MenuController
 * @package App\Http\Controllers\Admin\System
 */
class MenuSellerController extends BaseController
{
    /**
     * 菜单管理
     * @param Request $request
     */
    public function index(Request $request)
    {
        $menu = MenuSeller::getAll();
        return $this->success($menu);
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
            $data = MenuSeller::find($id);
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
        //验证规则
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'parent_id' => 'numeric',
            'position' => 'required|numeric',
            'url' => 'required',
        ], [
            'title.required' => '菜单名称不能为空',
            'parent_id.numeric' => '上级只能是数字',
            'position.required' => '排序不能为空',
            'position.numeric' => '排序只能是数字',
            'url.required' => '链接地址不能为空'
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['title', 'icon', 'parent_id', 'position', 'url']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $id = (int)$request->input('id');
        if ($id) {
            $res = MenuSeller::where('id', $id)->update($save_data);
        } else {
            $result = MenuSeller::create($save_data);
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
        $id = (int)$request->input('id');
        $status = (int)$request->input('status');
        if ($id && isset($status)) {
            $res = MenuSeller::where('id', $id)->update(['status' => $status]);
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
        $id = (int)$request->input('id');
        if (!$id) {
            api_error(__('admin.invalid_params'));
        }

        //查询是否存在下级分类
        $sub_menu = MenuSeller::where('parent_id', $id)->count();
        if ($sub_menu > 0) {
            api_error(__('admin.menu_child_no_empty'));
        }
        $res = MenuSeller::where('id', $id)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }
}
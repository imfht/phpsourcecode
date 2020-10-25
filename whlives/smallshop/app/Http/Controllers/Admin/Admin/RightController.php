<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\AdminRight;
use App\Models\Menu;
use Illuminate\Http\Request;
use Validator;

/**
 * 权限码
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class RightController extends BaseController
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
        $menu_child = (int)$request->input('menu_child');
        if ($title) $where[] = array('title', 'like', '%' . $title . '%');
        if ($menu_child) $where[] = array('menu_child', $menu_child);
        $res_list = AdminRight::select('id', 'title', 'menu_top', 'menu_child', 'created_at', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $menu_ids = array();
        foreach ($res_list as $value) {
            $menu_ids[] = $value['menu_top'];
            $menu_ids[] = $value['menu_child'];
        }
        if ($menu_ids) {
            $menu = Menu::whereIn('id', array_unique($menu_ids))->pluck('title', 'id');
        }
        $data_list = array();
        foreach ($res_list as $value) {
            $_item = $value;
            $_item['menu_top_name'] = isset($menu[$value['menu_top']]) ? $menu[$value['menu_top']] : '';
            $_item['menu_child_name'] = isset($menu[$value['menu_child']]) ? $menu[$value['menu_child']] : '';
            $data_list[] = $_item;
        }
        $total = AdminRight::where($where)->count();
        return $this->success($data_list, $total);
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
            $data = AdminRight::find($id);
            $data['right'] = array_to_br_textarea($data['right']);
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
            'menu_top' => 'required|numeric',
            'menu_child' => 'required|numeric',
            'right' => 'required'
        ], [
            'title.required' => '标题不能为空',
            'menu_top.required' => '菜单栏目不能为空',
            'menu_top.numeric' => '菜单栏目不能为空',
            'menu_child.required' => '菜单栏目不能为空',
            'menu_child.numeric' => '菜单栏目不能为空',
            'right.required' => '权限码不能为空'
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['title', 'menu_top', 'menu_child']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $right = $request->input('right');
        if ($right) {
            $rights = textarea_br_to_array($right);
            $save_data['right'] = join(',', $rights);
        }

        $id = (int)$request->input('id');
        if ($id) {
            $res = AdminRight::where('id', $id)->update($save_data);
        } else {
            $result = AdminRight::create($save_data);
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
            $res = AdminRight::whereIn('id', $ids)->update(['status' => $status]);
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
            api_error(__('admin.index_right_no_del'));
        }
        $res = AdminRight::whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 获取权限列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rights(Request $request)
    {
        $right_list = array();
        //获取权限列表
        $rights = AdminRight::where('status', AdminRight::STATUS_ON)->get();
        if ($rights) {
            foreach ($rights as $right) {
                $menu_ids[] = $right['menu_top'];
                $menu_ids[] = $right['menu_child'];
                $role_right[$right['menu_top']][$right['menu_child']][] = $right;
            }
            //菜单名称
            $menus = array();
            if ($menu_ids) {
                $menu_res = Menu::whereIn('id', array_unique($menu_ids))->get();
                if (!$menu_res->isEmpty()) {
                    $menus = array_column($menu_res->toArray(), 'title', 'id');
                }
            }
            foreach ($role_right as $key => $value) {
                $_item['name'] = isset($menus[$key]) ? $menus[$key] : '';
                $_item['id'] = $key;
                $child = array();
                foreach ($value as $k => $v) {
                    $_item_child['name'] = isset($menus[$k]) ? $menus[$k] : '';
                    $_item_child['id'] = $k;
                    $_item_child['right'] = $v;
                    $child[] = $_item_child;
                }
                $_item['right'] = $child;
                $right_list[] = $_item;
            }
        }
        if ($right_list) {
            return $this->success($right_list);
        } else {
            api_error(__('admin.content_is_empty'));
        }
    }
}
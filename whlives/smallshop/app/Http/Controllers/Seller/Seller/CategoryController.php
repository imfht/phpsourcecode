<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Seller\Seller;

use App\Http\Controllers\Seller\BaseController;
use App\Models\SellerCategory;
use Illuminate\Http\Request;
use Validator;

/**
 * 分类管理
 * Class MenuController
 * @package App\Http\Controllers\Admin\System
 */
class CategoryController extends BaseController
{
    /**
     * 分类管理
     * @param Request $request
     */
    public function index(Request $request)
    {
        $seller_id = $this->getUserId();
        $category = SellerCategory::getAll($seller_id);
        return $this->success($category);
    }

    /**
     * 根据id获取信息
     * @param Request $request
     * @return array
     */
    public function detail(Request $request)
    {
        $seller_id = $this->getUserId();
        $id = (int)$request->input('id');
        if ($id) {
            $data = SellerCategory::where(['id' => $id, 'seller_id' => $seller_id])->first();
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
        ], [
            'title.required' => '菜单名称不能为空',
            'parent_id.numeric' => '上级只能是数字',
            'position.required' => '排序不能为空',
            'position.numeric' => '排序只能是数字'
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $seller_id = $this->getUserId();
        $save_data = array();
        foreach ($request->only(['title', 'parent_id', 'image', 'position']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $id = (int)$request->input('id');
        if ($id) {
            $res = SellerCategory::where(['id' => $id, 'seller_id' => $seller_id])->update($save_data);
        } else {
            $save_data['seller_id'] = $seller_id;
            $result = SellerCategory::create($save_data);
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
        $seller_id = $this->getUserId();
        $id = (int)$request->input('id');
        $status = (int)$request->input('status');
        if ($id && isset($status)) {
            $res = SellerCategory::where(['id' => $id, 'seller_id' => $seller_id])->update(['status' => $status]);
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
        $seller_id = $this->getUserId();
        $id = (int)$request->input('id');
        if (!$id) {
            api_error(__('admin.invalid_params'));
        }

        //查询是否存在下级分类
        $sub_menu = SellerCategory::where(['parent_id' => $id, 'seller_id' => $seller_id])->count();
        if ($sub_menu > 0) {
            api_error(__('admin.category_child_no_empty'));
        }
        $res = SellerCategory::where(['id' => $id, 'seller_id' => $seller_id])->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 获取包含下级的下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function selectAll(Request $request)
    {
        $seller_id = $this->getUserId();
        $data = SellerCategory::getSelect($seller_id, 0, true);
        return $this->success($data);
    }

    /**
     * 获取下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        $seller_id = $this->getUserId();
        $parent_id = $request->input('parent_id', 0);
        $data = SellerCategory::getSelect($seller_id, $parent_id);
        return $this->success($data);
    }

    /**
     * 获取包含下级的多选下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function selectMulti(Request $request)
    {
        $seller_id = $this->getUserId();
        $data = SellerCategory::getSelect($seller_id, 0, true);
        $return = array();
        foreach ($data as $value) {
            $_item = array(
                'name' => $value['title'],
                'value' => $value['id']
            );
            $return[] = $_item;
            if (isset($value['children']) && $value['children']) {
                $_item['type'] = 'optgroup';
                foreach ($value['children'] as $value) {
                    $_children = array(
                        'name' => $value['title'],
                        'value' => $value['id']
                    );
                    $return[] = $_children;
                }
            }
        }
        return $this->success($return);
    }
}

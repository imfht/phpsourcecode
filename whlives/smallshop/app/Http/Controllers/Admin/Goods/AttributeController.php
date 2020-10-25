<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Http\Request;
use Validator;

/**
 * 商品属性
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class AttributeController extends BaseController
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
        $res_list = Attribute::select('id', 'title', 'input_type', 'category_id', 'note', 'position', 'created_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $category_ids = array();
        foreach ($res_list as $value) {
            $category_ids[] = $value['category_id'];
        }
        if ($category_ids) {
            $category = Category::whereIn('id', array_unique($category_ids))->pluck('title', 'id');
        }
        $data_list = array();
        foreach ($res_list as $value) {
            $_item = $value;
            $_item['input_type_text'] = Attribute::INPUT_TYPE_DESC[$_item['input_type']];
            $_item['category_name'] = isset($category[$value['category_id']]) ? $category[$value['category_id']] : '';
            $data_list[] = $_item;
        }
        $total = Attribute::where($where)->count();
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
            $data = Attribute::find($id);
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
            'input_type' => 'required',
            'category_id' => 'required|numeric',
            'position' => 'required|numeric'
        ], [
            'title.required' => '名称不能为空',
            'input_type.required' => '类型不能为空',
            'category_id.required' => '所属分类不能为空',
            'category_id.numeric' => '所属分类只能是数字',
            'position.required' => '排序不能为空',
            'position.numeric' => '排序只能是数字',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['title', 'input_type', 'category_id', 'note', 'position']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $id = (int)$request->input('id');
        if ($id) {
            $res = Attribute::where('id', $id)->update($save_data);
        } else {
            $result = Attribute::create($save_data);
            $res = $result->id;
        }
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
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
        $res = Attribute::whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 修改单个字段值
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fieldUpdate(Request $request)
    {
        $id = (int)$request->input('id');
        $field = $request->input('field');
        $field_value = $request->input('field_value');
        $field_arr = ['position'];//支持修改的字段
        if (in_array($field, $field_arr)) {
            if ($field == 'position') $field_value = (int)$field_value;
            if ($id && $field && $field_value) {
                $res = Attribute::where('id', $id)->update([$field => $field_value]);
                if ($res) {
                    return $this->success();
                } else {
                    api_error(__('admin.fail'));
                }
            }
        }
        api_error(__('admin.invalid_params'));
    }

    /**
     * 获取下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        $result = Attribute::orderBy('id', 'desc')
            ->pluck('title', 'id');
        return $this->success($result);
    }
}
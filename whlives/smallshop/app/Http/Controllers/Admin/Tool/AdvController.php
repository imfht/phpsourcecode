<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Tool;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Adv;
use Illuminate\Http\Request;
use Validator;

/**
 * 广告
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class AdvController extends BaseController
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
        $group_id = (int)$request->input('group_id');
        if ($title) $where[] = array('title', 'like', '%' . $title . '%');
        if ($group_id) $where[] = array('group_id', $group_id);
        $res_list = Adv::select('id', 'title', 'image', 'target_type', 'target_value', 'position', 'start_at', 'end_at', 'created_at', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $data_list = array();
        foreach ($res_list as $value) {
            $_item = $value;
            $_item['target_type'] = Adv::TARGET_TYPE_DESC[$value['target_type']];
            $data_list[] = $_item;
        }
        $total = Adv::where($where)->count();
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
            $data = Adv::find($id);
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
            'group_id' => 'required|numeric',
            'target_type' => 'required',
            'target_value' => 'required',
            'position' => 'required|numeric',
            'start_at' => 'required|date_format:"Y-m-d H:i:s"',
            'end_at' => 'required|date_format:"Y-m-d H:i:s"',
        ], [
            'title.required' => '名称不能为空',
            'group_id.required' => '广告组不能为空',
            'group_id.numeric' => '广告组只能是数字',
            'target_type.required' => '跳转类型',
            'target_value.required' => '跳转url或id不能为空',
            'position.required' => '排序不能为空',
            'position.numeric' => '排序只能是数字',
            'start_at.required' => '开始时间不能为空',
            'start_at.date_format' => '开始时间格式错误',
            'end_at.required' => '结束时间不能为空',
            'end_at.date_format' => '结束时间格式错误',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['title', 'group_id', 'image', 'target_type', 'target_value', 'position', 'start_at', 'end_at']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $id = (int)$request->input('id');
        if ($id) {
            $res = Adv::where('id', $id)->update($save_data);
        } else {
            $result = Adv::create($save_data);
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
            $res = Adv::whereIn('id', $ids)->update(['status' => $status]);
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
        $res = Adv::whereIn('id', $ids)->delete();
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
                $res = Adv::where('id', $id)->update([$field => $field_value]);
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
     * 获取跳转类型
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function targetType(Request $request)
    {
        return $this->success(Adv::TARGET_TYPE_DESC);
    }
}
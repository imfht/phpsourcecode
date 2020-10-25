<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Validator;

/**
 * 支付方式
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class PaymentController extends BaseController
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
        $res_list = Payment::select('id', 'title', 'image', 'content', 'position', 'type', 'created_at', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $data_list = array();
        foreach ($res_list as $key => $value) {
            $_item = $value;
            $_item['type'] = Payment::TYPE_DESC[$value['type']];
            $data_list[] = $_item;
        }
        $total = Payment::where($where)->count();
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
            $data = Payment::find($id);
        }
        if (!$data) {
            api_error(__('admin.content_is_empty'));
        }
        $data['client_type'] = explode(',' ,$data['client_type']);
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
            'type' => 'required|numeric',
            'client_type' => 'required',
            'position' => 'required|numeric',
        ], [
            'title.required' => '标题不能为空',
            'type.required' => '类型不能为空',
            'type.numeric' => '类型只能是数字',
            'client_type.required' => '客户端类型不能为空',
            'position.required' => '排序不能为空',
            'position.numeric' => '排序只能是数字',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['title', 'image', 'type', 'content', 'position']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }
        $save_data['client_type'] = implode(',', $request->input('client_type'));

        $id = (int)$request->input('id');
        if ($id) {
            $res = Payment::where('id', $id)->update($save_data);
        } else {
            $result = Payment::create($save_data);
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
        $status = (int)$request->status;
        if ($ids && isset($status)) {
            $res = Payment::whereIn('id', $ids)->update(['status' => $status]);
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
                $res = Payment::where('id', $id)->update([$field => $field_value]);
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
     * 获取类型列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getClientType(Request $request)
    {
        $type = Payment::CLIENT_TYPE_DESC;
        return $this->success($type);
    }
}
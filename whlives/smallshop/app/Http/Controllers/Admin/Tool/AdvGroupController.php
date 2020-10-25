<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Tool;

use App\Http\Controllers\Admin\BaseController;
use App\Models\AdvGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

/**
 * 广告组
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class AdvGroupController extends BaseController
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
        $res_list = AdvGroup::select('id', 'title', 'code', 'width', 'height', 'created_at', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $total = AdvGroup::where($where)->count();
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
            $data = AdvGroup::find($id);
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
            'code' => [
                'required',
                'numeric',
                'digits:3',
                Rule::unique('adv_group')->ignore($request->id)
            ],
            'width' => 'required|numeric',
            'height' => 'required|numeric'
        ], [
            'title.required' => '名称不能为空',
            'code.required' => 'code不能为空',
            'code.numeric' => 'code只能是数字',
            'code.digits' => 'code只能是3位数字',
            'code.unique' => 'code已经存在',
            'width.required' => '宽度不能为空',
            'width.numeric' => '宽度只能是数字',
            'height.required' => '高度不能为空',
            'height.numeric' => '高度只能是数字'
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $save_data = array();
        foreach ($request->only(['title', 'code', 'width', 'height', 'content']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $id = (int)$request->input('id');
        if ($id) {
            $res = AdvGroup::where('id', $id)->update($save_data);
        } else {
            $result = AdvGroup::create($save_data);
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
            $res = AdvGroup::whereIn('id', $ids)->update(['status' => $status]);
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
        $res = AdvGroup::whereIn('id', $ids)->delete();
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
        $where = array(
            array('status', AdvGroup::STATUS_ON),
        );
        $result = AdvGroup::where($where)
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');
        return $this->success($result);
    }
}
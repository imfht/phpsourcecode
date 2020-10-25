<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Seller\Market;

use App\Http\Controllers\Seller\BaseController;
use App\Models\Promotion;
use App\Models\Seller;
use Illuminate\Http\Request;
use Validator;

/**
 * 促销活动
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class PromotionController extends BaseController
{
    /**
     * 列表获取
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        $seller_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        //搜索
        $where = array();
        $where[] = array('seller_id', $seller_id);
        $title = $request->input('title');
        if ($title) $where[] = array('title', 'like', '%' . $title . '%');
        $res_list = Promotion::select('id', 'title', 'use_price', 'content', 'seller_id', 'status', 'start_at', 'end_at', 'created_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        //查询商家
        $seller_ids = array();
        foreach ($res_list as $value) {
            $seller_ids[] = $value['seller_id'];
        }
        if ($seller_ids) {
            $seller_data = Seller::whereIn('id', array_unique($seller_ids))->pluck('username', 'id');
        }
        $data_list = array();
        foreach ($res_list as $key => $value) {
            $_item = $value;
            $_item['username'] = isset($seller_data[$value['seller_id']]) ? $seller_data[$value['seller_id']] : '';
            $data_list[] = $_item;
        }
        $total = Promotion::where($where)->count();
        return $this->success($data_list, $total);
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
            $data = Promotion::where(['id' => $id, 'seller_id' => $seller_id])->first();
        }
        if (!$data) {
            api_error(__('admin.content_is_empty'));
        }
        $data['user_group'] = explode(',', $data['user_group']);
        if ($data['type'] == Promotion::TYPE_COUPONS) {
            $data['coupons_id'] = $data['type_value'];
            $data['type_value'] = '';
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
            'use_price' => 'required|price',
            'start_at' => 'required|date_format:"Y-m-d H:i:s"',
            'end_at' => 'required|date_format:"Y-m-d H:i:s"',
            'user_group' => 'required',
            'type' => 'required|numeric',
        ], [
            'title.required' => '标题不能为空',
            'use_price.required' => '起用金额不能为空',
            'use_price.price' => '起用金额格式错误',
            'start_at.required' => '开始时间不能为空',
            'start_at.date_format' => '开始时间格式错误',
            'end_at.required' => '结束时间不能为空',
            'start_at.date_format' => '结束时间格式错误',
            'user_group.required' => '用户组不能为空',
            'type.required' => '类型不能为空',
            'type.numeric' => '类型只能是数字',

        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $seller_id = $this->getUserId();
        $use_price = $request->input('use_price');
        $type = $request->input('type');
        $type_value = (int)$request->input('type_value');
        $coupons_id = (int)$request->input('coupons_id');
        switch ($type) {
            case Promotion::TYPE_REDUCTION:
                if (!is_numeric($type_value) || $type_value < 0 || $type_value > $use_price) {
                    api_error('满减金额只能在0到' . $use_price . '之间');
                }
                break;
            case Promotion::TYPE_DISCOUNT:
                if (!$type_value || $type_value < 0 || $type_value > 100) {
                    api_error(__('admin.promotion_pct_error'));
                }
                break;
            case Promotion::TYPE_POINT:
                if (!is_numeric($type_value) || $type_value < 0) {
                    api_error(__('admin.promotion_point_error'));
                }
                break;
            case Promotion::TYPE_COUPONS:
                if (!$coupons_id) {
                    api_error(__('admin.promotion_coupons_id_error'));
                }
                $type_value = $coupons_id;
                break;
        }

        $save_data = array();
        foreach ($request->only(['title', 'use_price', 'start_at', 'end_at', 'type', 'content']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }
        $save_data['type_value'] = $type_value;
        $save_data['user_group'] = implode(',', $request->input('user_group'));

        $id = (int)$request->input('id');
        if ($id) {
            $res = Promotion::where(['id' => $id, 'seller_id' => $seller_id])->update($save_data);
        } else {
            $save_data['seller_id'] = $seller_id;
            $result = Promotion::create($save_data);
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
        $ids = $this->checkBatchId();
        $status = (int)$request->input('status');
        if ($ids && isset($status)) {
            $res = Promotion::where('seller_id', $seller_id)->whereIn('id', $ids)->update(['status' => $status]);
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
        $ids = $this->checkBatchId();
        $res = Promotion::where('seller_id', $seller_id)->whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 获取类型列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getType(Request $request)
    {
        $type = Promotion::TYPE_DESC;
        return $this->success($type);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Coupons;
use App\Models\Seller;
use Illuminate\Http\Request;
use Validator;

/**
 * 优惠券活动
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class CouponsController extends BaseController
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
        $seller_id = (int)$request->input('seller_id');
        $seller_title = $request->input('seller_title');
        if ($title) $where[] = array('title', 'like', '%' . $title . '%');
        if ($seller_id) $where[] = array('seller_id', $seller_id);
        if ($seller_title) {
            $seller_id = Seller::where('title', $seller_title)->value('id');
            if ($seller_id) {
                $where[] = array('seller_id', $seller_id);
            } else {
                api_error(__('admin.content_is_empty'));
            }
        }
        $res_list = Coupons::select('id', 'title', 'image', 'type', 'amount', 'use_price', 'seller_id', 'status', 'start_at', 'end_at', 'created_at')
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
            $seller_data = Seller::whereIn('id', array_unique($seller_ids))->pluck('title', 'id');
        }
        $data_list = array();
        foreach ($res_list as $key => $value) {
            $_item = $value;
            $_item['username'] = isset($seller_data[$value['seller_id']]) ? $seller_data[$value['seller_id']] : '';
            $data_list[] = $_item;
        }
        $total = Coupons::where($where)->count();
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
            $data = Coupons::find($id);
        }
        if (!$data) {
            api_error(__('admin.content_is_empty'));
        }
        $rule = json_decode($data['rule'], true);
        foreach ($rule as $key => $val) {
            $data[$key] = $val ? implode(',', $val) : '';
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
            'amount' => 'required|price',
            'use_price' => 'required|price',
            'start_at' => 'required|date_format:"Y-m-d H:i:s"',
            'end_at' => 'required|date_format:"Y-m-d H:i:s"',
            'type' => 'required|numeric',
        ], [
            'title.required' => '标题不能为空',
            'amount.required' => '优惠值不能为空',
            'amount.price' => '优惠值格式错误',
            'use_price.required' => '起用金额不能为空',
            'use_price.price' => '起用金额格式错误',
            'start_at.required' => '开始时间不能为空',
            'start_at.date_format' => '开始时间格式错误',
            'end_at.required' => '结束时间不能为空',
            'start_at.date_format' => '结束时间格式错误',
            'type.required' => '类型不能为空',
            'type.numeric' => '类型只能是数字',

        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $use_price = $request->input('use_price');
        $type = $request->input('type');
        $amount = $request->input('amount');
        switch ($type) {
            case Coupons::TYPE_REDUCTION:
                if (!is_numeric($amount) || $amount < 0 || $amount > $use_price) {
                    api_error('满减金额只能在0到' . $use_price . '之间');
                }
                break;
            case Coupons::TYPE_DISCOUNT:
                if (!$amount || $amount < 0 || $amount > 100) {
                    api_error(__('admin.coupons_pct_error'));
                }
                break;
        }

        $save_data = array();
        foreach ($request->only(['title', 'limit', 'type', 'use_price', 'amount', 'seller_id','start_at', 'end_at', 'image', 'note']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $rule = array();
        foreach ($request->only(['goods_id:in', 'goods_id:not_in', 'category_id:in', 'category_id:not_in', 'brand_id:in', 'brand_id:not_in']) as $key => $value) {
            if ($value) {
                $value = str_replace('，', ',', $value);
            }
            $rule[$key] = $value ? explode(',', $value) : '';
        }
        $save_data['rule'] = json_encode($rule);

        $id = (int)$request->input('id');
        if ($id) {
            $res = Coupons::where('id', $id)->update($save_data);
        } else {
            $result = Coupons::create($save_data);
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
            $res = Coupons::whereIn('id', $ids)->update(['status' => $status]);
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
        $res = Coupons::whereIn('id', $ids)->delete();
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
        $where = array();
        $seller_id = (int)$request->input('seller_id');
        if (!$seller_id) {
            api_error(__('admin.missing_params'));
        }
        $where[] = array('status', Coupons::STATUS_ON);
        $where[] = array('seller_id', $seller_id);
        $where[] = array('end_at', '>', get_date());

        $res_list = Coupons::where($where)
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');
        return $this->success($res_list);
    }
}
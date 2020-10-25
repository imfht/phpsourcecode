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
use App\Models\CouponsDetail;
use App\Models\Member;
use Illuminate\Http\Request;
use Validator;

/**
 * 优惠券明细
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class CouponsDetailController extends BaseController
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
        $code = $request->input('code');
        $coupons_id = (int)$request->input('coupons_id');
        $username = $request->input('username');
        if ($code) $where[] = array('code', $code);
        if ($coupons_id) $where[] = array('coupons_id', $coupons_id);
        if ($username) {
            $member_id = Member::where('username', $username)->value('id');
            if ($member_id) {
                $where[] = array('m_id', $member_id);
            } else {
                api_error(__('admin.content_is_empty'));
            }
        }
        $res_list = CouponsDetail::select('id', 'm_id', 'code', 'status', 'is_use', 'use_at', 'bind_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        //查询用户
        $m_ids = array();
        foreach ($res_list as $value) {
            $m_ids[] = $value['m_id'];
        }
        if ($m_ids) {
            $member_data = Member::whereIn('id', array_unique($m_ids))->pluck('username', 'id');
        }
        $data_list = array();
        foreach ($res_list as $key => $value) {
            $_item = $value;
            $_item['status'] = CouponsDetail::STATUS_DESC[$value['status']];
            $_item['is_use'] = CouponsDetail::USE_DESC[$value['is_use']];
            $_item['username'] = isset($member_data[$value['m_id']]) ? $member_data[$value['m_id']] : '';
            $data_list[] = $_item;
        }
        $total = CouponsDetail::where($where)->count();
        return $this->success($data_list, $total);
    }

    /**
     * 生成券
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function generate(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'num' => 'required|numeric',
        ], [
            'id.required' => '优惠券ID不能为空',
            'amount.numeric' => '优惠券ID错误',
            'num.required' => '生成数量不能为空',
            'num.numeric' => '数量错误',

        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $coupons_id = (int)$request->input('id');
        $num = (int)$request->input('num');
        if ($coupons_id && $num) {
            if ($num > 500) {
                api_error(__('admin.coupons_max_500'));
            }
            $coupons = Coupons::find($coupons_id);
            if (!$coupons) {
                api_error(__('admin.coupons_not_exists'));
            }
            if ($coupons['end_at'] < get_date()) {
                api_error(__('admin.coupons_overdue'));
            }
            if ($coupons['status'] != Coupons::STATUS_ON) {
                api_error(__('admin.coupons_status_error'));
            }
            //开始生成券
            $res = CouponsDetail::generate($coupons->toArray(), $num);
            if ($res) {
                return $this->success();
            } else {
                api_error(__('admin.save_error'));
            }
        } else {
            api_error(__('admin.invalid_params'));
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
            $res = CouponsDetail::whereIn('id', $ids)->update(['status' => $status]);
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
        $res = CouponsDetail::whereIn('id', $ids)->delete();
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.del_error'));
        }
    }

    /**
     * 绑定用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bind(Request $request)
    {
        $username = $request->input('username');
        $id = (int)$request->input('id');

        if (!$username || !$id) {
            api_error(__('admin.invalid_params'));
        }

        $m_id = Member::where('username', $username)->value('id');
        if (!$m_id) {
            api_error(__('admin.bind_user_error'));
        }

        $res = CouponsDetail::where('id', $id)->update(['m_id' => $m_id, 'bind_at' => get_date()]);
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.fail'));
        }
    }
}
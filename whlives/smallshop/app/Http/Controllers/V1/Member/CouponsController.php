<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1\Member;

use App\Http\Controllers\V1\BaseController;
use App\Models\Coupons;
use App\Models\CouponsDetail;
use App\Models\Seller;
use Illuminate\Http\Request;

class CouponsController extends BaseController
{
    /**
     * 获取优惠券
     * @param Request $request
     */
    public function obtain(Request $request)
    {
        $m_id = $this->getUserId();
        $coupons_id = (int)$request->post('coupons_id');
        if (!$coupons_id) {
            api_error(__('api.missing_params'));
        }

        $res = CouponsDetail::obtain($m_id, $coupons_id);
        if ($res === true) {
            return $this->success(true);
        } else {
            api_error($res);
        }
    }

    /**
     * 优惠券已使用列表
     * @param Request $request
     */
    public function isUse(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $where = [
            'm_id' => $m_id,
            'is_use' => CouponsDetail::USE_ON
        ];
        $data_list = self::getCoupons($where, $offset, $limit);

        $total = CouponsDetail::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 优惠券未使用列表
     * @param Request $request
     */
    public function normal(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $where = [
            ['m_id', $m_id],
            ['is_use', CouponsDetail::USE_OFF],
            ['status', CouponsDetail::STATUS_OFF],
            ['start_at', '<=', get_date()],
            ['end_at', '>=', get_date()]
        ];

        $data_list = self::getCoupons($where, $offset, $limit);
        $total = CouponsDetail::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 优惠券已过期列表
     * @param Request $request
     */
    public function overdue(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $where = [
            ['m_id', $m_id],
            ['is_use', CouponsDetail::USE_OFF],
            ['status', CouponsDetail::STATUS_OFF],
            ['end_at', '<=', get_date()]
        ];

        $data_list = self::getCoupons($where, $offset, $limit);
        $total = CouponsDetail::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 获取优惠券信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    private function getCoupons($where, $offset, $limit)
    {
        $res_list = CouponsDetail::select('id', 'coupons_id', 'start_at', 'end_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $coupons_ids = array();
        foreach ($res_list as $value) {
            $coupons_ids[] = $value['coupons_id'];
        }
        //获取优惠券信息
        if ($coupons_ids) {
            $coupons_res = Coupons::select('id as coupons_id', 'title', 'image', 'type', 'amount', 'use_price', 'note', 'seller_id')->whereIn('id', array_unique($coupons_ids))->get();
            if (!$coupons_res->isEmpty()) {
                $coupons_res = array_column($coupons_res->toArray(), null, 'coupons_id');
            }
            //获取商家信息
            $seller_ids = array();
            foreach ($coupons_res as $value) {
                $seller_ids[] = $value['seller_id'];
            }
            if ($seller_ids) {
                $seller_res = Seller::select('id', 'title')->whereIn('id', array_unique($seller_ids))->get();
                $seller_res = array_column($seller_res->toArray(), null, 'id');
            }
        }
        $data_list = array();
        foreach ($res_list->toArray() as $value) {
            $_coupons = isset($coupons_res[$value['coupons_id']]) ? $coupons_res[$value['coupons_id']] : array();
            if ($_coupons) {
                $_seller = isset($seller_res[$_coupons['seller_id']]) ? $seller_res[$_coupons['seller_id']] : array();
                $_item = array(
                    'title' => $_coupons['title'],
                    'image' => $_coupons['image'],
                    'type' => $_coupons['type'],
                    'amount' => $_coupons['amount'],
                    'use_price' => $_coupons['use_price'],
                    'note' => $_coupons['note'],
                    'start_at' => $value['start_at'],
                    'end_at' => $value['end_at'],
                    'seller_title' => $_seller['title']
                );
                $data_list[] = $_item;
            }
        }
        return $data_list;
    }
}

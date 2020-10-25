<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Trade;
use Illuminate\Http\Request;

/**
 * 交易单
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class TradeController extends BaseController
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
        $trade_no = $request->input('trade_no');
        $username = $request->input('username');
        $type = (int)$request->input('type');
        //搜索
        $where = array();
        $where[] = array('status', Trade::STATUS_ON);
        if ($trade_no) $where[] = array('trade_no', $trade_no);
        if ($type) $where[] = array('type', $type);
        if ($username) {
            $member_id = Member::where('username', $username)->value('id');
            if ($member_id) {
                $where[] = array('m_id', $member_id);
            } else {
                api_error(__('admin.content_is_empty'));
            }
        }

        $res_list = Trade::select('id', 'm_id', 'trade_no', 'type', 'subtotal', 'flag', 'payment_no', 'pay_total', 'payment_id', 'pay_at')
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
            $_item['subtotal'] = '￥' . $value['subtotal'];
            $_item['type'] = Trade::TYPE_DESC[$value['type']];
            $_item['username'] = isset($member_data[$value['m_id']]) ? $member_data[$value['m_id']] : '';
            $_item['payment'] = $value['payment_id'] ? Payment::PAYMENT_DESC[$value['payment_id']] : '';
            $data_list[] = $_item;
        }
        $total = Trade::where($where)->count();
        return $this->success($data_list, $total);
    }

    /**
     * 获取type类型
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function getType(Request $request)
    {
        $type = Trade::TYPE_DESC;
        return $this->success($type);
    }
}
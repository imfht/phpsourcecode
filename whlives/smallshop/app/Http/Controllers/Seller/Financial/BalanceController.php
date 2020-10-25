<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Seller\Financial;

use App\Http\Controllers\Seller\BaseController;
use App\Models\SellerBalance;
use App\Models\SellerBalanceDetail;
use App\Models\SellerWithdraw;
use Illuminate\Http\Request;
use Validator;

/**
 * 余额
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class BalanceController extends BaseController
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
        $where[] = array('m_id', $seller_id);

        $res_list = SellerBalanceDetail::select('id', 'm_id', 'type', 'event', 'detail_no', 'amount', 'balance', 'note', 'created_at')
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
            $_item['event'] = SellerBalanceDetail::EVENT_DESC[$value['event']];
            $_item['amount'] = ($value['type'] == SellerBalanceDetail::TYPE_RECR ? '-' : '+') . $value['amount'];
            $data_list[] = $_item;
        }
        $total = SellerBalanceDetail::where($where)->count();
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
        if ($seller_id) {
            $data = SellerBalance::where(['m_id' => $seller_id])->first();
        }
        if (!$data) {
            api_error(__('admin.content_is_empty'));
        }
        return $this->success($data);
    }

    /**
     * 提现提交
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function save(Request $request)
    {
        $seller_id = $this->getUserId();
        $amount = $request->post('amount');
        if (!$amount || !check_price($amount)) {
            api_error(__('admin.invalid_params'));
        }

        $note = '用户提现【' . $amount . '】';
        $res = SellerBalance::updateAmount($seller_id, '-' . $amount, SellerBalanceDetail::EVENT_WITHDRAW, '', $note);
        if ($res['status']) {
            $create_data = [
                'm_id' => $seller_id,
                'amount' => $amount
            ];
            $add = SellerWithdraw::create($create_data);
            if ($add) {
                return $this->success(true);
            } else {
                api_error(__('admin.fail'));
            }
        } else {
            api_error($res['message']);
        }
    }

}
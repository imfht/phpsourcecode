<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Admin\BaseController;
use App\Models\BalanceDetail;
use App\Models\Member;
use Illuminate\Http\Request;
use Validator;

/**
 * 余额明细
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class BalanceDetailController extends BaseController
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
        $m_id = (int)$request->input('m_id');
        $username = $request->input('username');

        //搜索
        $where = array();
        if ($m_id) $where[] = array('m_id', $m_id);
        if ($username) {
            $member_id = Member::where('username', $username)->value('id');
            if ($member_id) {
                $where[] = array('m_id', $member_id);
            } else {
                api_error(__('admin.content_is_empty'));
            }
        }

        $res_list = BalanceDetail::select('id', 'm_id', 'type', 'event', 'detail_no', 'amount', 'balance', 'note', 'created_at')
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
            $_item['event'] = BalanceDetail::EVENT_DESC[$value['event']];
            $_item['amount'] = ($value['type'] == BalanceDetail::TYPE_RECR ? '-' : '+') . $value['amount'];
            $data_list[] = $_item;
        }
        $total = BalanceDetail::where($where)->count();
        return $this->success($data_list, $total);
    }

}
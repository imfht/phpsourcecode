<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1\Member;

use App\Http\Controllers\V1\BaseController;
use App\Models\PointDetail;
use Illuminate\Http\Request;

class PointController extends BaseController
{
    /**
     * 余额明细列表
     * @param Request $request
     */
    public function detailList(Request $request)
    {
        $m_id = $this->getUserId();
        list($page, $limit, $offset) = get_page_params();
        $event = (int)$request->post('event');
        $where = [
            'm_id' => $m_id
        ];
        if ($event) {
            $where['event'] = $event;
        }
        $res_list = PointDetail::select('id', 'type', 'event', 'detail_no', 'amount', 'created_at as create_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $data_list = array();
        foreach ($res_list as $value) {
            $_item = $value;
            $_item['event'] = PointDetail::EVENT_DESC[$value['event']];
            $_item['amount'] = ($value['type'] == PointDetail::TYPE_INCR ? '+' : '-') . $value['amount'];
            $_item['create_at'] = substr($value['create_at'], 0, 10);
            unset($_item['type']);
            $data_list[] = $_item;
        }
        $total = PointDetail::where($where)->count();
        $return = [
            'lists' => $data_list,
            'total' => $total,
        ];
        return $this->success($return);
    }

    /**
     * 余额明细
     * @param Request $request
     */
    public function detail(Request $request)
    {
        $m_id = $this->getUserId();
        $id = (int)$request->post('id');
        if (!$id) {
            api_error(__('api.missing_params'));
        }
        $where = [
            'm_id' => $m_id,
            'id' => $id
        ];
        $res_list = PointDetail::select('event', 'detail_no', 'amount', 'balance', 'note', 'created_at as create_at')->where($where)->first();
        if (!$res_list) {
            api_error(__('api.content_is_empty'));
        }

        $res_list['event'] = PointDetail::EVENT_DESC[$res_list['event']];
        return $this->success($res_list);
    }
}

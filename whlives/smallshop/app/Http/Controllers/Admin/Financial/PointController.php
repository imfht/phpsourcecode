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
use App\Models\Point;
use App\Models\PointDetail;
use Illuminate\Http\Request;
use Validator;

/**
 * 积分
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class PointController extends BaseController
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
        $res_list = Point::select('id', 'm_id', 'amount', 'updated_at')
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
            $_item['username'] = isset($member_data[$value['m_id']]) ? $member_data[$value['m_id']] : '';
            $data_list[] = $_item;
        }
        $total = Point::where($where)->count();
        return $this->success($data_list, $total);
    }

    /**
     * 批量充值
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function batchRecharge(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'amount' => 'required|price',
        ], [
            'username.required' => '用户名不能为空',
            'amount.required' => '金额不能为空',
            'amount.price' => '积分格式错误',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $username = str_replace('，', ',', $request->input('username'));
        $username = explode(',', $username);
        //查询id是否都存在
        $is_exists = $member = array();
        foreach ($username as $value) {
            $member_id = Member::where('username', $value)->value('id');
            if (!$member_id){
                $is_exists[] = $value;
            } else {
                $member[$member_id] = $value;
            }
        }
        if ($is_exists) {
            api_error('1|用户名' . join(',', $is_exists) . '不存在');
        }

        if (!$member) {
            api_error(__('admin.invalid_params'));
        }
        //全部通过开始充值
        $amount = $request->input('amount');
        $note = $request->input('note');
        $error_username = array();
        foreach (array_keys($member) as $val) {
            $res = Point::updateAmount($val, $amount, PointDetail::EVENT_SYSTEM_RECHARGE, '', $note);
            if (!$res['status']) {
                $error_username[] = $member_id[$val];
            }
        }

        if ($error_username) {
            api_error('1|用户名' . join(',', $is_exists) . '充值失败');
        } else {
            return $this->success();
        }
    }

    /**
     * 充值
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function recharge(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'm_id' => 'required|numeric',
            'amount' => 'required|price',
        ], [
            'm_id.required' => '用户id错误',
            'm_id.numeric' => '用户id错误',
            'amount.required' => '金额不能为空',
            'amount.price' => '积分格式错误',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $m_id = (int)$request->input('m_id');
        $amount = $request->input('amount');
        $note = $request->input('note');

        $res = Point::updateAmount($m_id, $amount, PointDetail::EVENT_SYSTEM_RECHARGE, '', $note);

        if ($res['status']) {
            return $this->success();
        } else {
            api_error($res['message']);
        }
    }

    /**
     * 扣除
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function deduct(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'm_id' => 'required|numeric',
            'amount' => 'required|price',
        ], [
            'm_id.required' => '用户id错误',
            'm_id.numeric' => '用户id错误',
            'amount.required' => '金额不能为空',
            'amount.price' => '积分格式错误',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $m_id = (int)$request->input('m_id');
        $amount = $request->input('amount');
        $note = $request->input('note');

        $res = Point::updateAmount($m_id, -$amount, PointDetail::EVENT_SYSTEM_DEDUCT, '', $note);

        if ($res['status']) {
            return $this->success();
        } else {
            api_error($res['message']);
        }
    }
}
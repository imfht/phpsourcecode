<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1\Member;

use App\Http\Controllers\V1\BaseController;
use App\Models\Member;
use App\Models\MemberAuth;
use App\Models\MemberProfile;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class IndexController extends BaseController
{
    /**
     * 个人中心首页
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        $m_id = $this->getUserId();
        $member = $this->getUserInfo();

        //待付款订单
        $wait_pay_num = Order::where(['m_id' => $m_id, 'status' => Order::STATUS_WAIT_PAY])->count();
        //待收货订单
        $wait_send_num = Order::where(['m_id' => $m_id, 'status' => Order::STATUS_SHIPMENT])->count();
        //待评价订单
        $wait_comment_num = Order::where(['m_id' => $m_id, 'status' => Order::STATUS_DONE])->count();
        //售后订单
        $refund_num = Refund::where(['m_id' => $m_id])->whereNotIn('status', [Refund::STATUS_DONE, Refund::STATUS_CUSTOMER_CANCEL])->count();
        $auth_type = MemberAuth::where(['m_id' => $m_id])->pluck('type')->toArray();
        $return = array(
            'nickname' => $member['nickname'],
            'headimg' => $member['headimg'],
            'wait_pay_num' => $wait_pay_num,
            'wait_send_num' => $wait_send_num,
            'wait_comment_num' => $wait_comment_num,
            'refund_num' => $refund_num,
            'is_bind_qq' => in_array(MemberAuth::TYPE_QQ, $auth_type) ? 1 : 0,
            'is_bind_wechat' => in_array(MemberAuth::TYPE_WECHAT, $auth_type) ? 1 : 0,
            'is_bind_weibo' => in_array(MemberAuth::TYPE_WEIBO, $auth_type) ? 1 : 0
        );
        return $this->success($return);
    }

    /**
     * 个人资料
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function info(Request $request)
    {
        $m_id = $this->getUserId();
        $member = Member::where('id', $m_id)->first();
        if (!$member) {
            api_error(__('api.invalid_token'));
        }
        $profile = $member->profile;
        $user_info = array(
            'nickname' => $member['nickname'],
            'headimg' => $member['headimg'],
            'full_name' => $profile['full_name'],
            'tel' => $profile['tel'],
            'email' => $profile['email'],
            'sex' => MemberProfile::SEX_DESC[$profile['sex']]
        );
        return $this->success($user_info);
    }

    /**
     * 修改个人资料
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function saveInfo(Request $request)
    {
        $m_id = $this->getUserId();
        $member_data = $profile_data = array();
        foreach ($request->only(['nickname', 'headimg']) as $key => $value) {
            $member_data[$key] = ($value || $value == 0) ? $value : null;
        }

        foreach ($request->only(['full_name', 'tel', 'email', 'sex']) as $key => $value) {
            $profile_data[$key] = ($value || $value == 0) ? $value : null;
        }

        if ($member_data) {
            Member::where('id', $m_id)->update($member_data);
        }
        if ($profile_data) {
            MemberProfile::where('member_id', $m_id)->update($profile_data);
        }
        return $this->success(true);
    }

    /**
     * 修改用户密码
     * @return array
     */
    public function upPassword(Request $request)
    {
        $old_password = $request->post('old_password');
        $new_password = $request->post('new_password');
        if (!$old_password || !$new_password) {
            api_error(__('api.missing_params'));
        }
        $m_id = $this->getUserId();
        $member_data = Member::find($m_id);
        if (!Hash::check($old_password, $member_data['password'])) {
            api_error(__('api.old_password_error'));
        }

        $update_data['password'] = Hash::make($new_password);
        $res = Member::where('id', $m_id)->update($update_data);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 设置用户支付密码
     * @return array
     */
    public function setPayPassword(Request $request)
    {
        $password = $request->post('password');
        if (!$password) {
            api_error(__('api.missing_params'));
        }
        $m_id = $this->getUserId();
        $member_data = Member::find($m_id);
        if ($member_data['pay_password']) {
            api_error(__('api.pay_password_isset'));
        }

        $update_data['pay_password'] = Hash::make($password);
        $res = Member::where('id', $m_id)->update($update_data);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 修改用户支付密码
     * @return array
     */
    public function upPayPassword(Request $request)
    {
        $old_password = $request->post('old_password');
        $new_password = $request->post('new_password');
        if (!$old_password || !$new_password) {
            api_error(__('api.missing_params'));
        }
        $m_id = $this->getUserId();
        $member_data = Member::find($m_id);
        if (!Hash::check($old_password, $member_data['pay_password'])) {
            api_error(__('api.old_pay_password_error'));
        }

        $update_data['pay_password'] = Hash::make($new_password);
        $res = Member::where('id', $m_id)->update($update_data);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 第三方解绑
     */
    public function removeAuthBind(Request $request)
    {
        $m_id = $this->getUserId();
        $type = $request->post('type');
        if (!isset(MemberAuth::TYPE_DESC[$type])) {
            api_error(__('api.missing_params'));
        }
        $res = MemberAuth::where(['m_id' => $m_id, 'type' => $type])->delete();
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }
}

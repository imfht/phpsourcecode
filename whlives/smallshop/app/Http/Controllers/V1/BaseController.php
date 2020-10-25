<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Models\Member;
use App\Models\MemberGroup;
use App\Services\TokenService;

class BaseController extends \App\Http\Controllers\BaseController
{
    /**
     * 获取用户id
     */
    public function getUserId() {
        $token_service = new TokenService('api');
        $token = $token_service->getToken();
        if (isset($token['id']) && $token['id']) {
            return $token['id'];
        } else {
            return false;
        }
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo() {
        $m_id = $this->getUserId();
        $member = Member::where('id', $m_id)->first();
        if (!$member) {
            api_error(__('api.invalid_token'));
        }
        $profile = $member->profile;
        $user_info = array(
            'id' => $member['id'],
            'username' => $member['username'],
            'nickname' => $member['nickname'],
            'headimg' => $member['headimg'],
            'status' => $member['status'],
            'group_id' => $member['group_id'],
            'full_name' => $profile['full_name'],
        );
        return $user_info;
    }
}

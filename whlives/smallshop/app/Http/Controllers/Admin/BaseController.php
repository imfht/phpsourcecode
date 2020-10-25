<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 4:19 PM
 */

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Services\TokenService;

class BaseController extends \App\Http\Controllers\BaseController
{
    /**
     * 批量修改或编辑的时候验证id
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function checkBatchId() {
        $id = request()->input('id');
        if (is_array($id)) {
            foreach ($id as $val) {
                $_id = (int)$val;
                if ($_id) {
                    $ids[] = $_id;
                }
            }
        } else {
            $ids = array((int)$id);
        }

        if (!$ids) {
            api_error(__('admin.invalid_params'));
        }
        return $ids;
    }

    /**
     * 获取用户id
     */
    public function getUserId() {
        $token_service = new TokenService('admin');
        $token = $token_service->getToken();
        if (isset($token['id']) && $token['id']) {
            return $token['id'];
        } else {
            api_error(__('admin.invalid_token'));
        }
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo() {
        $user_id = $this->getUserId();
        $user_data = Admin::find($user_id);
        return $user_data;
    }
}

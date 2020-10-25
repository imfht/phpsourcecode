<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Seller\Member;

use App\Http\Controllers\Seller\BaseController;
use App\Models\MemberGroup;
use Illuminate\Http\Request;
use Validator;

/**
 * 会员分组
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class GroupController extends BaseController
{
    /**
     * 获取下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        //角色
        $role = MemberGroup::where('status', MemberGroup::STATUS_ON)
            ->pluck('title', 'id');
        return $this->success($role);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Seller\System;

use App\Http\Controllers\Seller\BaseController;
use App\Models\ExpressCompany;
use Illuminate\Http\Request;
use Validator;

/**
 * 快递公司
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class ExpressCompanyController extends BaseController
{

    /**
     * 快递公司列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        $where = array(
            'status' => ExpressCompany::STATUS_ON
        );
        $res_list = ExpressCompany::where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');
        return $this->success($res_list);
    }
}
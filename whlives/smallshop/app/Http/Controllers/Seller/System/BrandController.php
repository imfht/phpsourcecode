<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Seller\System;

use App\Http\Controllers\Seller\BaseController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Validator;

/**
 * 品牌
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class BrandController extends BaseController
{
    /**
     * 获取下拉列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function select(Request $request)
    {
        $brand = Brand::where('status', Brand::STATUS_ON)
            ->pluck('title', 'id');
        return $this->success($brand);
    }
}
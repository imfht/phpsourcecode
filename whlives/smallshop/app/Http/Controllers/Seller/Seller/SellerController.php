<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Seller\Seller;

use App\Http\Controllers\Seller\BaseController;
use App\Models\Seller;
use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

/**
 * 商家管理
 * Class ExpressCompanyController
 * @package App\Http\Controllers\Admin\System
 */
class SellerController extends BaseController
{
    /**
     * 当前用户信息
     * @param Request $request
     * @return array
     */
    public function info(Request $request)
    {
        $seller_id = $this->getUserId();
        $seller = Seller::find($seller_id);
        $seller_profile = SellerProfile::where('seller_id', $seller_id)->first();
        return $this->success(array_merge($seller->toArray(),$seller_profile->toArray()));
    }

    /**
     * 修改当前用户信息
     * @param Request $request
     * @return array
     */
    public function infoUpdate(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'invoice' => 'required|numeric',
            'tel' => 'required',
            'email' => 'required|email',
        ], [
            'image.required' => 'logo能为空',
            'invoice.required' => '发票不能为空',
            'invoice.numeric' => '发票只能是数字',
            'tel.required' => '电话不能为空',
            'email.required' => '邮箱地址不能为空',
            'email.email' => '邮箱格式错误'
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $seller_data = array();
        foreach ($request->only(['image', 'invoice']) as $key => $value) {
            $seller_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $save_data = array();
        foreach ($request->only(['tel', 'email', 'content']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }

        $password = $request->input('password');
        if ($password) {
            $seller_data['password'] = Hash::make($password);
        }

        $seller_id = $this->getUserId();
        $res = Seller::where('id', $seller_id)->update($seller_data);
        SellerProfile::where('seller_id', $seller_id)->update($save_data);
        if ($res) {
            return $this->success();
        } else {
            api_error(__('admin.save_error'));
        }
    }
}
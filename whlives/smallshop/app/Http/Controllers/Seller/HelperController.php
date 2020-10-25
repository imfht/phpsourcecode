<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/1
 * Time: 下午2:58
 */

namespace App\Http\Controllers\Seller;

use App\Libs\Aliyun\AliyunOss;
use App\Models\Areas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Mews\Captcha\Facades\Captcha;

class HelperController extends BaseController
{
    const MODEL_TYPE = ['image', 'editor', 'head', 'video', 'comment'];//文件上传类型

    /**
     * 获取阿里云webtoken
     * @param Request $request
     */
    public function aliyunToken(Request $request)
    {
        $model = $request->input('model', 'image');
        if (!in_array($model, self::MODEL_TYPE)) {
            api_error(__('admin.upload_model'));
        }
        $token = Cache::remember('aliyun_web_token' . $model, 120, function () use ($model) {
            $aliyunoss = new AliyunOss();
            $token = $aliyunoss->getWebToken($model);
            return $token;
        });
        return $this->success($token);
    }

    /**
     * 获取地区
     * @param Request $request
     * @return array
     */
    public function area(Request $request)
    {
        $parent_id = (int)$request->input('parent_id');
        $area_list = Areas::getArea($parent_id);
        return $this->success($area_list);
    }


    /**
     * 验证码
     * @param Request $request
     * @return array|void
     * @throws \App\Exceptions\ApiException
     */
    public function captcha(Request $request)
    {
        $aa = Captcha::create('flat', true);
        return $this->success($aa);
    }
}

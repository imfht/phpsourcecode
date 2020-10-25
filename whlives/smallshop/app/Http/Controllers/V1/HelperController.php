<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Libs\Aliyun\AliyunOss;
use App\Libs\Sms;
use App\Libs\Weixin\Wechat;
use App\Models\Adv;
use App\Models\AdvGroup;
use App\Models\Areas;
use App\Models\ExpressCompany;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HelperController extends BaseController
{
    const MODEL_TYPE = ['image', 'editor', 'head', 'video', 'comment'];//文件上传类型

    /**
     * 发送验证码
     * @param Request $request
     */
    public function captcha(Request $request)
    {
        $type = $request->post('type');
        $mobile = $request->post('mobile');
        $sms_type = ['login', 'reg', 'find_password', 'reset_password'];
        if (!in_array($type, $sms_type)) {
            api_error(__('api.captcha_type'));
        }
        if (!check_mobile($mobile)) {
            api_error(__('api.mobile_format'));
        }
        //find_password、reset_password需要验证手机号码是否正确
        if (in_array($type, ['find_password', 'reset_password'])) {
            if (!Member::where('username', $mobile)->exists()) {
                api_error(__('api.user_mobile_error'));
            }
        }
        //检测是否已经发送
        $device = get_device();
        $redis_key = 'captcha:' . $mobile;
        $captcha = cache($redis_key);
        if ($captcha && $captcha['end_at'] > (time() - config('sms.interval_time'))) {
            api_error(__('api.sms_frequent'));
        }
        $send_data = array(
            'code' => rand(1000, 9999),
        );
        $sms = new Sms();
        $res = $sms->send($send_data, $type, $mobile);
        if ($res) {
            $log_data = array(
                'mobile' => $mobile,
                'device' => $device,
                'code' => $send_data['code'],
                'end_at' => time()
            );
            cache([$redis_key => $log_data], config('sms.out_time'));
            return $this->success(true);
        } else {
            api_error(__('api.sms_send_fail'));
        }
    }

    /**
     * 获取阿里云webtoken
     * @param Request $request
     */
    public function aliyunToken(Request $request)
    {
        $model = $request->input('model', 'image');
        if (!in_array($model, self::MODEL_TYPE)) {
            api_error(__('api.upload_model'));
        }
        $token = Cache::remember('aliyun_web_token' . $model, 120, function () use ($model) {
            $aliyunoss = new AliyunOss();
            $token = $aliyunoss->getWebToken($model);
            return $token;
        });
        return $this->success($token);
    }

    /**
     * 获取阿里云sts
     * @param Request $request
     */
    public function aliyunSts(Request $request)
    {
        $model = $request->post('model', 'image');
        if (!in_array($model, self::MODEL_TYPE)) {
            api_error(__('api.upload_model'));
        }
        $img_dir = 'dev_upload';
        if (!config('app.debug')) {
            $img_dir = 'uploads';
        }
        $file_name = md5(time() . Str::random(10));
        $dir = $img_dir . '/' . $model . '/' . substr($file_name, 0, 2) . '/' . substr($file_name, 2, 2) . '/' . substr($file_name, 4, 2) . '/';
        $aliyunoss = new AliyunOss();
        $token = $aliyunoss->getSts();
        $token['dir'] = $dir;
        $token['domain'] = config('app.img_domain');
        $token['endpoint'] = config('aliyun.oss.endpoint');
        $token['bucket'] = config('aliyun.oss.bucket');
        return $this->success($token);
    }

    /**
     * 根据广告位获取广告列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function adv(Request $request)
    {
        $code = (int)$request->code;
        if (!$code) {
            api_error(__('api.missing_params'));
        }
        $group_where = array(
            ['code', $code],
            ['status', AdvGroup::STATUS_ON]
        );
        $group_id = AdvGroup::where($group_where)->value('id');
        if (!$group_id) {
            api_error(__('api.content_is_empty'));
        }

        $adv_where = array(
            ['group_id', $group_id],
            ['status', Adv::STATUS_ON],
            ['start_at', '<=', get_date()],
            ['end_at', '>=', get_date()]
        );
        $res_list = Adv::select('title', 'image', 'target_type', 'target_value')
            ->where($adv_where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        return $this->success($res_list);
    }

    /**
     * 获取地区
     * @param Request $request
     * @return array
     */
    public function area(Request $request)
    {
        $parent_id = (int)$request->parent_id;
        $area_list = Areas::getArea($parent_id);
        return $this->success($area_list);
    }

    /**
     * 快递公司列表
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function expressCompany(Request $request)
    {
        $where = array(
            'status' => ExpressCompany::STATUS_ON
        );
        $res_list = ExpressCompany::select('id', 'title')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        return $this->success($res_list);
    }

    /**
     * 微信jssdk
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function wxJssdk(Request $request)
    {
        $url = $request->input('url');
        if (!$url) {
            api_error(__('api.missing_params'));
        }
        $mp = new Wechat();
        $app = $mp->getApp();
        $app->jssdk->setUrl($url);
        $jssdk = $app->jssdk->buildConfig(array(), false, $beta = false, $json = false);
        return $this->success($jssdk);
    }
}

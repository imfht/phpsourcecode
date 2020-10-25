<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/1
 * Time: 下午4:03
 */

/**
 * 获取用户id
 * @param string $type 类型token默认api
 * @return bool
 */
function get_user_id($type = 'api')
{
    $token_service = new \App\Services\TokenService($type);
    $token = $token_service->getToken();
    if (isset($token['id']) && $token['id']) {
        return $token['id'];
    } else {
        return false;
    }
}

/**
 * 获取用户用户组
 * @return bool
 */
function get_user_group()
{
    $return = array(
        'group_id' => 0,
        'pct' => 0,
        'title' => ''
    );
    $m_id = get_user_id();
    if ($m_id) {
        $cache_key = 'user_group:' . $m_id;
        $cache_data = cache($cache_key);
        if ($cache_data) {
            $return = $cache_data;
        } else {
            $group_id = \App\Models\Member::where('id', $m_id)->value('group_id');
            if ($group_id) {
                $group = \App\Models\MemberGroup::where(['id' => $group_id, 'status' => \App\Models\MemberGroup::STATUS_ON])->first();
                if ($group) {
                    $pct = $group['pct'] / 100;
                    if ($pct > 1) $pct = 1;
                    if ($pct < 0) $pct = 0;
                    $return['group_id'] = $group_id;
                    $return['pct'] = $pct;
                    $return['title'] = $group['title'];
                }
                cache([$cache_key => $return], 600);
            }
        }
    }
    return $return;
}

/**
 * 获取设备号
 * @return mixed
 */
function get_device()
{
    $device = request()->cookie('device');
    if (!$device) {
        $device = request()->input('device');
        if (!$device) {
            $device = request()->device;
        }
    }

    return $device;
}

/**
 * 获取平台类型
 * @return mixed
 */
function get_platform()
{
    //web网页，h5移动端网页，mp微信，wechat小程序，ios，android
    $platform = request()->cookie('platform');
    if (!$platform) {
        $platform = request()->input('platform');
        if (!$platform) {
            $platform = request()->platform;
        }
    }

    return strtolower($platform);
}

/**
 * 获取apikey
 * @return mixed
 */
function get_api_key()
{
    $api_key = '';
    //web网页，h5移动端网页，mp微信，wechat小程序，ios，android
    $platform = get_platform();
    if (in_array($platform, ['ios', 'android'])) {
        $api_key = config('app.api_key_app');
    } elseif (in_array($platform, ['web', 'h5', 'mp'])) {
        $api_key = config('app.api_key_h5');
    } elseif (in_array($platform, ['wechat'])) {
        $api_key = config('app.api_key_wechat');
    }
    return $api_key;
}

/**
 * 获取手机型号
 * @return mixed
 */
function get_mobile_model()
{
    $mobile_model = request()->cookie('mobile_model');
    if (!$mobile_model) {
        $mobile_model = request()->input('mobile_model');
        if (!$mobile_model) {
            $mobile_model = request()->mobile_model;
        }
    }

    return strtolower($mobile_model);
}

/**
 * 转换时间
 * @param $time date时间
 * @param bool $conver 是否转成时间戳
 * @return string
 */
function get_date_time($time, $conver = false)
{
    if (!$time) {
        return $conver ? 0 : '';
    }
    if ($time == '0000-00-00 00:00:00') {
        return $conver ? 0 : '';
    }
    if ($conver) {
        return strtotime($time);
    } else {
        return $time;
    }
}

/**
 * 阿里云等比缩放图片，限定在矩形框内
 * $w=宽，$h=高
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
function resize_images($image_url, $w = 0, $h = 0)
{
    if (strpos($image_url, 'http') === false) {
        return $image_url;
    } else {
        $image = $w ? $image_url . '?x-oss-process=image/resize,m_lfit,' . 'h_' . $h . ',w_' . $w : $image_url;

        return $image;
    }
}


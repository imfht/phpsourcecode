<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace util;

/**
 * 官方授权处理执行类
 * @author 牧羊人
 * @date 2019/4/23
 * Class Accredit
 * @package util
 */
class Accredit
{

    // 请求地址
    const RXTHINK_ACCREDIT_URL = 'http://www.rxthink.com/client_product_accredit';

    /**
     * 授权执行
     * @param array $data 参数
     * @return mixed 返回结果
     * @author 牧羊人
     * @date 2019/4/23
     */
    public static function runAccredit($data = [])
    {
        $data = array_merge([
            'product_name' => 'rxthink',
            'product_verion' => '2.0',
            'build_verion' => '201901010001',//编译版本
            'domain' => request()->domain(),
        ], $data);

        $data['agent'] = $_SERVER['HTTP_USER_AGENT'];
        //$result = curl_post(self::rxthink_ACCREDIT_URL, $data);
        $result = [];
        $result['data'] = get_random_str(32);
//        $result = json_decode($result, true);
        $install_lock = $result['data'];

        // 生成安装文件锁
        file_put_contents(ROOT_PATH . 'install.lock', json_encode($install_lock));
        return $install_lock;
    }

    /**
     * 获取产品授权token
     * @return string 返回结果
     * @author 牧羊人
     * @date 2019/4/23
     */
    public function getAccreditToken()
    {
        $token = Cache::get('accredit_token');
        if (!$token) {
            $install_lock = json_decode(file_get_contents(ROOT_PATH . 'install.lock'), true);
            if ($install_lock) {
                $token = isset($install_lock['access_token']) ? $install_lock['access_token'] : '';
                Cache::set('accredit_token', $token, 3600 * 3);
            }
        }
        return $token;
    }

    /**
     * 版本检测（获取云端版本）
     * @return mixed
     * @author 牧羊人
     * @date 2019/4/23
     */
    public function getVersion()
    {
        $version_info = Cache::get('rxthink_remote_version');
        if (!$version_info) {
            $rxthink_version = rxthink_V;
            $url = config('rxthink_api_url') . '/rxthink_version?epv=' . urlencode($rxthink_version);
            $result = curl_get($url);
            $version_info = json_decode($result, true);
            Cache::set('rxthink_remote_version', $version_info, 3600);
        }
        return $version_info;
    }

    /**
     * 获取官方动态
     * @param array $data 返回结果
     * @return mixed
     * @author 牧羊人
     * @date 2019/4/23
     */
    public function getOfficialNews($data = [])
    {
        $rxthink_news = Cache::get('rxthink_news');
        if (!$rxthink_news) {
            $url = config('rxthink_api_url') . '/client_rxthink_news';
            $result = curl_post($url, $data);
            $rxthink_news = json_decode($result, true);
            Cache::set('rxthink_news', $rxthink_news, 3600 * 3);
        }

        return $rxthink_news;
    }

    /**
     * 身份验证
     * @return array 返回结果
     * @author 牧羊人
     * @date 2019/4/23
     */
    public function identification()
    {
        try {
            $rxthink_identification = cache('rxthink_identification');
            if (!$rxthink_identification || !is_array($rxthink_identification)) {
                //需要重新登录
                throw new \Exception("请登录验证身份", 2);
            } else {
                $uid = $rxthink_identification['uid'];
                $access_token = $rxthink_identification['access_token'];
                // 请求地址
                $url = config('rxthink_api_url') . '/api/user';
                $result = curl_request($url, ['uid' => $uid, 'token' => $access_token], 'GET');

                $result = json_decode($result['content'], true);
                if ($result['code'] == 1) {
                    $return = [
                        'code' => 1,
                        'msg' => '身份验证成功',
                        'data' => $result['data']['userinfo'],
                    ];
                    return $return;
                } else {
                    if ($result['code'] == 2) {
                        cache('rxthink_identification', null);
                    }
                    //需要重新登录
                    throw new \Exception($result['msg'], $result['code']);
                }
            }
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => [],
            ];
        }
    }
}

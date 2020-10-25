<?php
/**
 * Created by PhpStorm.
 * User: yingouqlj
 * Date: 17/1/13
 * Time: 下午5:15
 */

namespace extend\weapp\api;

class CreateQrCode extends BaseApi
{
    //限制二维码接口
    const LIMIT_API = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode';
    //不限制小程序接口
    const UNLIMIT_API = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit';
    const NEED_ACCESS_TOKEN = true;
    protected $grant_type = 'client_credential';
    const CURL_RAW = true;

    public $expires_in;

    /**
     * 获取小程序码，永久的
     * @param $path
     * @param $width
     * @return resource
     */
    public function createWeQr($path, $scene, $width = 480)
    {
        $params = [
            'page' => $path,
            'width' => $width,
            'scene' => $scene
        ];
        return $this->query(self::UNLIMIT_API . '?access_token=' . $this->accessToken, $params, 'post');

    }

    /**
     * 获取二维码临时的
     * @param $path
     * @param $scene
     * @param int $width
     * @return mixed
     * @throws \Exception
     */
    public function createQr($path, $scene, $width = 480)
    {
        $params = [
            'path' => $path . '?id=' . $scene,
            'width' => $width
        ];
        return $this->query(self::LIMIT_API . '?access_token=' . $this->accessToken, $params, 'post');

    }


}
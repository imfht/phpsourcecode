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


use AlibabaCloud\Client\AlibabaCloud;

class AliSMS
{
    // 用户识别号
    private $accessKeyId;
    // 验证秘钥
    private $accessKeySecret;
    // 区域ID
    private $regionId;
    // 短信签名
    private $signName;
    // 模板ID
    private $templateCode;

    /**
     * 初始化
     * AliSMS constructor.
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public function __construct()
    {
        // 用户标识号
        $this->accessKeyId = "";
        // 验证秘钥
        $this->accessKeySecret = "";
        // 区域ID
        $this->regionId = "cn-hangzhou";
        // 短信签名
        $this->signName = "大学圈";
        // 模板ID
        $this->templateCode = "";

        // 初始化SDK
        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)
            ->regionId($this->regionId)
            ->asDefaultClient();
    }

    /**
     * 发送短信
     * @param $mobile 手机号码
     * @param $code 6位数验证码
     * @param string $errorMsg 错误信息
     * @return bool 返回结果：true成功，false失败
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     * @author 牧羊人
     * @date 2019/11/5
     */
    public function sendSms($mobile, $code, &$errorMsg = '')
    {
        // 模板替换标签
        $templateParam = json_encode(['code' => $code], JSON_UNESCAPED_UNICODE);
        // 发送参数
        $param = [
            'RegionId' => $this->regionId,
            'PhoneNumbers' => $mobile,
            'SignName' => $this->signName,
            'TemplateCode' => $this->templateCode,
            'TemplateParam' => "$templateParam"
        ];

        // 执行发送类
        $result = self::sendSdk($param);
        file_put_contents(RUNTIME_PATH . "sms.txt", json_encode($result));
        // 错误信息
        $errorMsg = isset($result['Message']) ? $result['Message'] : '';
        // 返回结果
        if (strtoupper($result['Code']) === 'OK') {
            // 发送成功
            return true;
        } else {
            // 发送失败
            return false;
        }
    }

    /**
     * 调用SDK发送短信
     * @param $param
     * @return array
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     * @author 牧羊人
     * @date 2019/11/5
     */
    public static function sendSdk($param)
    {
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options(['query' => $param])
                ->request();
            //$res['Message'] == 'OK' && $res['Code'] == 'OK'   发送成功
            return $res = $result->toArray();
        } catch (ClientException $e) {
            return ['Code' => 'Error', 'Message' => $e->getErrorMessage()];
        } catch (ServerException $e) {
            return ['Code' => 'Error', 'Message' => $e->getErrorMessage()];
        }
    }
}
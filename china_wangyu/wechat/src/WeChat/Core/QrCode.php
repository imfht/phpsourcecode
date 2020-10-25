<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Core;

include(__DIR__.'/../Lib/phpqrcode.php');

/**
 * Class Qrcode 二维码类
 * @package wechat
 */
class QrCode extends Base
{
    // 获取微信公众二维码（永久/有效时长）
    private static $setQrCodeUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN';


    // 显示微信公众号二维码
    private static $showqrcodeUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=JSAPI_TICKET';


    /**
     * 生成二维码
     * @inheritdoc 文档说明：http://phpqrcode.sourceforge.net/
     * @param string $text 二维码内容
     * @param bool $filePath    二维码储存路径
     * @param string $level 二维码容错机制
     * @param int $size 点大小
     * @param int $margin   点间距
     * @param bool $saveandprint    保存或打印
     * @return string|void
     */
    public static function create(string $text = '',
                                  bool $filePath = false,
                                  string $level = QR_ECLEVEL_L,
                                  int $size = 6,
                                  int $margin = 2,
                                  bool $saveandprint=false)
    {
        try {
            if ($filePath !== false) {
                // Save it to a file
                if (!is_dir(dirname($filePath))) {
                    mkdir(dirname($filePath), 755);
                }
            }
             return \QRcode::png($text,$filePath,$level,$size,$margin,$saveandprint);
        } catch (\WeChat\Extend\Json $exception) {
            return $exception->getMessage();
        }

    }

    /**
     * 创建微信带参二维码生成
     * @inheritdoc 详细文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433542
     * @param string $accessToken 授权TOKEN
     * @param string $scene_str 字符串
     * @param string $scene_str_prefix 字符串前缀
     * @param int $type 二维码类型：(小于等于1) = 有效时长30天  (大于等于2) = 永久
     * @return array|bool|mixed
     */
    public static function wechat(string $accessToken,string $scene_str, string $scene_str_prefix = 'wene_', int $type = 1)
    {
        $result = false;
        // 验证微信普通token
        empty($accessToken) && $accessToken = Token::gain();

        //创建加密字符
        $strLen = strlen($scene_str) + strlen($scene_str_prefix);

        // 验证字符长度
        if ($strLen <= 64 and $strLen > 1) {
            // 准备参数
            $setQrCodeUrl = str_replace('TOKEN', $accessToken, static::$setQrCodeUrl);

            $qrCodeParam['action_name'] = "QR_LIMIT_STR_SCENE";
            if(intval($type) <= 1){
                $qrCodeParam['action_name'] = "QR_STR_SCENE";
                $qrCodeParam['expire_seconds'] = 604800;
            }
            $qrCodeParam['action_info'] = [
                'scene'=> ['scene_str'=> $scene_str_prefix . $scene_str],
            ];
            $qrCodeParam = json_encode($qrCodeParam,JSON_UNESCAPED_UNICODE);

            // 获取对应数据
            $result = self::post($setQrCodeUrl, $qrCodeParam);
            if (isset($result['ticket'])) {
                $result['showUrl'] = str_replace('JSAPI_TICKET',$result['ticket'],static::$showqrcodeUrl);
            }
        }
        // 返回结果
        return $result;
    }
}

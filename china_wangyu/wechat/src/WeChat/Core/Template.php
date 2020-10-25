<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Core;

/**
 * Class WxTemplate 微信模板类
 * @package wechat
 */
class Template extends Base
{
    // 微信获取所有模板api
    private static $getTemplateUrl = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=TOKEN';

    /**
     * 格式化消息模板内容
     * @param array $template 模板内容
     * @return array    消息模板内容
     */
    public static function format($template = [])
    {
        $param = self::trim_template($template['content']);
        $template['param'] = [
            'touser' => '', // 用户OPENID
            'template_id' => $template['template_id'], //模板ID
            'url' => '', // 跳转的url地址
            'topcolor' => '',
            'data' => $param, //模板必须参数
        ];
        return $template;
    }

    /**
     * 获取所有消息模板内容
     * @inheritdoc 详细文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     * @param string $accessToken 微信token
     * @return array
     */
    public static function gain(string $accessToken)
    {
        static::$getTemplateUrl = str_replace('TOKEN',$accessToken,static::$getTemplateUrl);
        return self::get(static::$getTemplateUrl);
    }

    /**
     * 获取模板需要的参数name
     * @param string $string 过滤包含参数的字符串
     * @return array    不带其它字符的参数数组
     */
    private static function trim_template(string $string)
    {
        $string = preg_replace('/([\x80-\xff]*)/i', '', $string);
        $trim = array(" ", "　", "\t", "\n", "\r", '.DATA', '}}');
        $arr = explode('{{', str_replace($trim, '', $string));
        unset($arr[0]);
        return array_values($arr);
    }

}

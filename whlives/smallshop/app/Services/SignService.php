<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/18
 * Time: 下午4:03
 */

namespace App\Services;

/**
 * 签名验证
 * Class SginService
 * @package App\Services
 */
class SignService
{
    /**
     * 除去数组中的空值和签名参数
     * @param $post_data post数据
     * @return array
     */
    public function arrayFilter($post_data) {
        $para_filter = array();
        foreach ($post_data as $key => $val) {
            if ($key == "sign" || $val == "" || is_array($val)) {
                continue;
            } else {
                $para_filter[$key] = $post_data[$key];
            }
        }
        ksort($para_filter);
        reset($para_filter);
        return $para_filter;
    }

    /**
     * 生成签名结果
     * @param $data_filter 参与签名的数据
     * @return string
     */
    public function buildSign($data_filter) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($data_filter);
        //把拼接后的字符串再与安全校验码直接连接起来
        $prestr = $prestr . '&key=' . get_api_key();
        //把最终的字符串签名，获得签名结果
        $mysgin = md5($prestr);
        return strtoupper($mysgin);
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $data_filter 要拼接的数据
     * @return string
     */
    public function createLinkstring($data_filter) {
        $arg = "";
        foreach ($data_filter as $key => $val) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = trim($arg, '&');
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }
}
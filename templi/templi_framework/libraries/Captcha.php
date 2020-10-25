<?php
/**
 * 验证码
 * @author 七殇酒
 * @email 739800600@qq.com
 * @lastmodify   2013-4-25
 *
 */
namespace framework\libraries;

class Captcha
{
    /**
     * 生成图像验证码
     * @static
     * @access public
     * @param int|string $length 位数
     * @param int|string $mode 类型 0 字母 1 数字 2大写字母 3小写字母 4汉字 其它混合
     * @param array|string $size = array(width,height)  图片大小宽度
     * @param array $font =array(font,fontsize)  字体
     * @param int $disturb 是否干扰 1 点干扰 2 线干扰 3 复合干扰 0 无干扰 4 间断直线
     * @param bool $border 是否要边框
     * @param string $type 图像格式
     * @param string $verifyName session key
     * @return string
     */
    static function buildImageVerify($length=4, $mode=3, $size=array(70, 30), $font=array(), $disturb=3, $border=true,$type='png', $verifyName='verify') {
        require_once('String.class.php');
        require_once('Image.class.php');
        require_once('Session.php');
        $session = new Session();
        $randval = String::randString($length, $mode);
        $session->set($verifyName, strtolower($randval));
        return Image::buildString($randval, $size, $font, $filename='',$rgb=array(), $type, $disturb, $border);

    }
}
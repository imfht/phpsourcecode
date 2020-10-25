<?php
namespace Thumb;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OAth
 *
 * @author Administrator
 */
class CheckParam {
    /*
     * 设置图片大小 长和宽
     * @param object size 
     * @return bool
     */
    public function resize($seze)
    {
        return $this->_oauthWH($seze,\Thumb\Config::seze);        
    }

    /*
     * 把图片等比缩小到原来的百分数，比如50就是原来的50%。
     * @param int p 
     * @return bool
     */
    public function resizePercent($p)
    {
        return $this->_oauthInt($p,\Thumb\Config::percent);
    }

    /*
     * 截取一个175px * 175px的图片，注意这个是截取，超出的部分直接裁切掉，不是强制改变尺寸。
     * @param object $adaptive 
     * @return bool
     */
    public function adaptiveResize($adaptive)
    {
    
        return $this->_oauthWH($adaptive,\Thumb\Config::adaptive);
    }

    /*
     * 从图片的中心计算，截取200px * 100px的图片。
     * @param object center
     * @return bool
     */
    public function cropFromCenter($center)
    {
        return $this->_oauthWH($center,\Thumb\Config::center);
    }

    /*
     * 把图片顺时针反转180度
     * @param int rotate
     * @return bool
     */
    public function rotateImageNDegrees($rotate)
    {
        return $this->_oauthInt($rotate,\Thumb\Config::rotate);
    }
    /*
     * 验证宽高是否合法
     * @param array $wh 二位数组  array('width'=>'','height'=>'')
     * @param json $config 一个json字符串
     * @return bool
     */
    private function _oauthWH($wh,$config)
    {
        if(empty($wh['height']))
        {
            $wh['height'] = $wh['width'];
        }
        if($config === 0)
        {
            return true;
        }
        if(false === $config)
        {
            return false;
        }
        $config = json_decode($config,true);
        if(isset($config[$wh['width']]) and $config[$wh['width']] == $wh['height'])
        {
            return true;
        }
        else
        {
            return false;
        }
        return true;
    }
    
    /*
     * 验证只需要一个值的参数对象
     * @param int $int  array('width'=>'','height'=>'')
     * @param json $config 一个json字符串
     * @return bool
     */
    private function _oauthInt($int,$config)
    {
        if($config === 0)
        {
            return true;
        }
        if(false === $config)
        {
            return false;
        }
        $config = json_decode($config,true);
        if(isset($config[1]))
        {
            if($config[0] < $p and $config[1] > $int)
            {
                return true;
            }
            return false;
        }
        else
        {
            if(isset($config[0]) and $config[0] == $int)
            {
               return true;
            }
            return false;
        }
    }
}

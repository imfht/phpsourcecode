<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/8
 * Time: 15:24
 */

namespace naples\lib\weiChat;


use weiChat\Wechat;

class NPweiChat extends Wechat
{
    /**
     * 设置缓存，按需重载
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename,$value,$expired){
        cache($cachename,$value,$expired);
        return true;
    }

    /**
     * 获取缓存，按需重载
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename){
        $rel= cache($cachename);
        if (isFlagNotSet($rel)){return '';}
        return $rel;
    }

    /**
     * 清除缓存，按需重载
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename){
        cache($cachename,null,-1);
        return true;
    }
}
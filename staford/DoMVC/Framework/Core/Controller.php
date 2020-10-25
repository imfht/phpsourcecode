<?php

/**
 * 控制器基础类文件
 * @author 暮雨秋晨
 * @copyright 2014
 */

require_once 'C/ControllerException.php';

class Controller extends Template
{
    private static $MSG_file = null;

    /**
     * 设置提示页面
     */
    public static function setMsgFile($file)
    {
        return self::$MSG_file = $file;
    }

    /**
     * 判断是否是POST访问
     */
    protected function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] === 'POST');
    }

    /**
     * 跳转提示
     * @param string $msg 提示信息
     * @param string $url 跳转地址
     * @param integer $time 等待时长
     */
    protected function msg($msg = '操作成功', $url = '', $time = 3)
    {
        if (self::$MSG_file) {
            $this->assign('msg', $msg);
            $this->assign('url', $url);
            $this->assign('time', $time);
            $this->display(self::$MSG_file);
        } else {
            include FRAMEWORK . 'message.html';
        }
        exit;
    }
}

?>
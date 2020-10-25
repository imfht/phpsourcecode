<?php

/**
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Debug
{
    private static $debugMsg = array(); //DEBUG信息存放

    public static function addMsg($msg)
    {
        $msg = trim($msg);
        self::$debugMsg[] = $msg;
    }

    public static function getMsg($isAll = false)
    {
        if ($isAll) {
            return self::$debugMsg;
        }
        if (!empty(self::$debugMsg)) {
            return self::$debugMsg[0];
        }
        return false;
    }
}

?>
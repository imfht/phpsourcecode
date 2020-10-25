<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\GEO;

use \Cute\Utility\IP;
use \Cute\Utility\Binary;

/**
 *
 * 国家IP数据库
 * 下载 https://db-ip.com/db/download/country
 *
 * +-----------------------+
 * +       文件头          +   //索引区第一条(4B) + 索引区最后一条(4B)
 * +-----------------------+
 * +       记录区          +   //(记录: 国家缩写(2B) + \0) * n
 * +-----------------------+
 * +       索引区          +   //(索引: IP地址(4B) * 2 + 记录偏移量(3B)) * n
 * +-----------------------+
 *
 */
class IPCountry extends Binary
{

    protected $term_size = 4;       //数据项定长，4字节
    protected $offset_size = 2;     //偏移量定长，2字节

    /**
     * 将IP对象或字符串ip转为HEX格式
     */

    public static function formatIP($ipaddr)
    {
        return IP::toHex($ipaddr);
    }

    public function isStopNearStart()
    {
        return true;
    }

    public function writeIP($ipaddr)
    {
        $hex = self::formatIP($ipaddr);
        return $this->writeHex($hex);
    }

    public function readZone()
    {
        $code = $this->readString();
        return trim($code);
    }

    /**
     * 查找IP详细位置
     */
    public function search($ipaddr)
    {
        //将要判断的IP转为4个字节HEX
        $ipaddr = self::formatIP($ipaddr);
        $this->findLineStart($ipaddr);
        $curr_stop = $this->findLineStop();
        if (strcmp($ipaddr, $curr_stop) <= 0) {
            return $this->readZone();
        } else {
            return '';
        }
    }

}

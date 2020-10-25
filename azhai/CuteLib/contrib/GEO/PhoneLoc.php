<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\GEO;

use \Cute\Utility\Binary;


/**
 *
 * 号码归属地 phoneloc.dat
 * 下载
 *
 * +-----------------------+
 * +       文件头          +   //索引区第一条(4B) + 索引区最后一条(4B)
 * +-----------------------+
 * +       记录区          +   //(记录: 省 + \t + 市 + \t + 运营商 + \0) * m
 * +-----------------------+
 * +       索引区          +   //(索引: 电话头部(3B) * 2 + 记录偏移量(3B)) * n
 * +-----------------------+
 *
 */
class PhoneLoc extends Binary
{
    protected $term_size = 3;       //数据项定长，3字节
    protected $offset_size = 3;     //偏移量定长，3字节

    public function isStopNearStart()
    {
        return true;
    }

    public function writeTel($tel, $return = false)
    {
        $tel = self::formatTel($tel);
        return $this->writeNumber($tel, $this->term_size, $return);
    }

    /**
     * 格式化电话号码，国际区号保留，中国大陆区号去除
     */
    public static function formatTel($number)
    {
        $number = str_replace('+', '00', trim($number));
        if (starts_with($number, '00')) { //去0后取前7位
            $number = '9' . str_pad($number, 6, '0');
        }
        $number = substr(ltrim($number, '0'), 0, 7);
        return intval($number);
    }

    public function readZone()
    {
        //可能有1字节\0分隔符
        return $this->readString($this->firstChar());
    }

    /**
     * 查找号码归属地
     */
    public function search($number)
    {
        //将要判断的号码转为3个字节HEX
        $number = self::formatTel($number);
        $number = self::padHex($number, $this->term_size);
        $this->findLineStart($number);
        $curr_stop = $this->findLineStop();
        if (strcmp($number, $curr_stop) <= 0) {
            return $this->readZone();
        }
    }
}

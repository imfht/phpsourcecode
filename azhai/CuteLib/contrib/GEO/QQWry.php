<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\GEO;

use \Cute\Utility\IP;
use \Cute\Contrib\GEO\IPCountry;


/**
 *
 * 纯真IP数据库 QQWry.dat
 * 下载 http://www.cz88.net/fox/ipdat.shtml
 *
 * +-----------------------+
 * +       文件头          +   //索引区第一条(4B) + 索引区最后一条(4B)
 * +-----------------------+
 * +       记录区          +   //记录 * m
 * +-----------------------+
 * +       索引区          +   //(索引: IP地址(4B) + 记录偏移量(3B)) * n
 * +-----------------------+
 *
 */
class QQwry extends IPCountry
{
    const BYTE_FLAG = 1;            //标志位长度，1字节
    const FLAG_TERMINATE = 0;       //终止，也不在当前位置读取
    const FLAG_JUMP_FIRST = 1;      //首次跳转
    const FLAG_JUMP_SENIOR = 2;     //高级跳转

    protected $term_size = 4;       //数据项定长，纯真IP库是4字节
    protected $offset_size = 3;     //偏移量定长，纯真IP库是3字节

    public function isStopNearStart()
    {
        return false;
    }

    /**
     * 读取分区，根据标志位1/2跳转或再次跳转
     *  * 都在当前
     *  2 分区跳转、位置当前
     *  1* 分区跳转、位置当前（已跳转）
     *  12 分区再跳转、位置当前（已跳转）
     */
    public function readZone()
    {
        $flag = $this->readInt(self::BYTE_FLAG);
        if ($flag === self::FLAG_JUMP_FIRST) {
            $offset = $this->readNumber($this->offset_size);
            $this->seek($offset);
            return $this->readZone();
        } else if ($flag === self::FLAG_JUMP_SENIOR) { // 仅分区跳转
            $offset = $this->readNumber($this->offset_size);
            $loc = $this->readLocation();
            $this->seek($offset);
            $zone = $this->readString();
        } else { //在当前位置读取
            $zone = $this->readString(chr($flag));
            $loc = $this->readLocation();
        }
        $zone = trim($zone) ? convert(trim($zone), 'UTF-8') : '';
        $loc = trim($loc) ? convert(trim($loc), 'UTF-8') : '';
        return [$zone, $loc];
    }

    /**
     * 读取位置，根据标志位0/1/2跳转
     */
    public function readLocation()
    {
        $flag = $this->readInt(self::BYTE_FLAG);
        if ($flag === self::FLAG_TERMINATE) {
            return ''; //没有可读数据，返回空字符串
        } else if ($flag === self::FLAG_JUMP_FIRST
            || $flag === self::FLAG_JUMP_SENIOR
        ) { //重定向后读取
            $offset = $this->readNumber($this->offset_size);
            $this->seek($offset);
            return $this->readString();
        } else { //在当前位置读取
            return $this->readString(chr($flag));
        }
    }
}

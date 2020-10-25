<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Utility;


/**
 * 二进制文件写入
 */
trait BinaryWriter
{
    /**
     * 删减文件内容
     */
    public function truncate($remain_size = 0)
    {
        return ftruncate($this->fp, $remain_size);
    }

    /**
     * 写入文件
     */
    public function write($data)
    {
        return fwrite($this->fp, $data);
    }

    /**
     * 写入一个整数，1、2、4、8字节
     */
    public function writeInt($data, $bytes = 4, $return = false)
    {
        $type = 'C';
        if ($bytes === 2) {
            $type = $this->isBOM() ? 'n' : 'v';
        } else if ($bytes === 4) {
            $type = $this->isBOM() ? 'N' : 'V';
        } else if ($bytes === 8) {
            $type = $this->isBOM() ? 'J' : 'P';
        }
        $bin = pack($type, $data);
        return $return ? $bin : $this->write($bin);
    }

    /**
     * 写入若干字节的HEX，一个字节是两个Hex
     */
    public function writeHex($hex, $bytes = false, $return = false)
    {
        if (empty($bytes)) {
            $bytes = ceil(strlen($hex) / 2);
        }
        $hex = self::padHex($hex, $bytes, 'left');
        if (!$this->isBOM() && $bytes > 1) {
            $hex = implode('', array_reverse(str_split($hex, 2)));
        }
        $bin = @hex2bin($hex);
        return $return ? $bin : $this->write($bin);
    }

    /**
     * 写入一个整数，若干字节
     */
    public function writeNumber($data, $bytes = 3, $return = false)
    {
        $hex = dechex(intval($data));
        return $this->writeHex($hex, $bytes, $return);
    }

    /**
     * 写入字符串，默认添加\0结尾
     */
    public function writeString($string, $end_char = null, $return = false)
    {
        $string .= (is_string($end_char) ? $end_char : chr(0));
        return $return ? $string : $this->write($string);
    }

    /**
     * 写入头部信息，可选的版本号
     */
    public function writeHeaders($version = '')
    {
        $this->seek(0);
        $this->writeInt($this->index_first);   #第一条索引区位置，4字节
        $this->writeInt($this->index_last);    #最后一条索引区位置，4字节
        if ($version) {
            $this->writeString($version);
        }
    }

    /**
     * 写入偏移量
     */
    public function writeOffset($position)
    {
        return $this->writeNumber($position, $this->offset_size);
    }

    /**
     * 在末尾加入全部index数据
     */
    public function appendIndexes(Binary& $idat)
    {
        $this->index_first = $this->tell();
        $bytes = $idat->tell();
        if ($bytes === 0) {
            $idat->seek(0, SEEK_END);
            $bytes = $idat->tell();
        }
        $idat->seek(0);
        $position = $this->write($idat->read($bytes));
        $index_size = $this->getIndexSize();
        $this->index_last = $this->tell() - $index_size;
        return $this->writeNumber($position, $this->term_size);
    }
}

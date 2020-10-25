<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Utility;


/**
 * 二进制文件
 *
 * 例如：纯真IP数据库QQWry.dat
 * +-----------------------+
 * +       文件头          +   //索引区第一条(4B) + 索引区最后一条(4B)
 * +-----------------------+
 * +       记录区          +   //记录 * m
 * +-----------------------+
 * +       索引区          +   //(索引: IP地址(4B) + 记录偏移量(3B)) * n
 * +-----------------------+
 */
class Binary
{
    use \Cute\Base\Deferring;

    protected $filename = '';       //数据文件名
    protected $fp = null;           //文件字节流
    protected $term_size = 0;       //数据项定长，纯真IP库是4字节
    protected $offset_size = 0;     //偏移量定长，纯真IP库是3字节
    protected $index_first = 0;     //第一条索引位置
    protected $index_last = 0;      //最后一条索引位置
    protected $index_total = 0;     //索引数量

    public function __construct($filename = '', $term_size = 0, $offset_size = 0)
    {
        $this->filename = $filename;
        if ($term_size > 0) {
            $this->term_size = $term_size;
        }
        if ($offset_size > 0) {
            $this->offset_size = $offset_size;
        }
    }

    public function initiate($writing = false)
    {
        if (empty($this->filename)) {
            $this->fp = tmpfile(); //临时文件句柄
            return $this;
        }
        if (!file_exists($this->filename)) {
            @mkdir(dirname($this->filename), 0755, true);
            touch($this->filename, 0666);
        }
        $mode = $writing ? 'wb' : 'rb';
        $this->fp = fopen($this->filename, $mode);
        return $this;
    }

    public function close()
    {
        if ($this->fp) {
            fclose($this->fp);
            $this->fp = null; //必须，避免重复关闭
        }
    }

    /**
     * 文件编码是否高位在前
     */
    public function isBOM()
    {
        return false;
    }

    /**
     * 结束项是否紧跟在开始项之后
     */
    public function isStopNearStart()
    {
        return false;
    }

    public function getIndexSize()
    {
        $count = $this->isStopNearStart() ? 2 : 1;
        return $this->term_size * $count + $this->offset_size;
    }

    /**
     * 补齐16进制的位数
     */
    public static function padHex($hex, $bytes, $orient = 'left')
    {
        if (is_int($hex)) {
            $hex = dechex($hex);
        }
        $pad_type = (strtolower($orient) === 'left') ? STR_PAD_LEFT : STR_PAD_RIGHT;
        return str_pad($hex, $bytes * 2, '0', $pad_type);
    }

    /**
     * 二分（折半）查找算法
     */
    public static function binSearch(& $object, $method, $target,
                                     $total, $index_size)
    {
        $left = 0;
        $right = $total;
        do {
            $middle = $left + floor(($right - $left) / 2);
            $sign = $object->$method($middle, $target, $index_size);
            if ($sign > 0) { //目标在右侧
                $left = $middle;
            } else if ($sign < 0) { //目标在左侧
                $right = $middle;
            } else {
                break;
            }
        } while ($right - $left > 1);
        return $sign;
    }

    /**
     * 读取和比对数据项
     */
    public function compare($offset, $target, $index_size)
    {
        $this->seek($this->index_first + $offset * $index_size);
        $current = $this->readHex($this->term_size); //开头
        $this->seek(-$this->term_size, SEEK_CUR); //回退当前索引开头
        return strcmp($target, $current); // 指出下次偏移的方向
    }

    /**
     * 查找目标所在行
     */
    public function findLineStart($target)
    {
        if (!$this->fp) {
            $this->initiate();
            $this->readHeaders();
        }
        $this->seek($this->index_first);
        // 比较并决定方向
        $index_size = $this->getIndexSize();
        $sign = self::binSearch($this, 'compare', $target, $this->index_total, $index_size);
        // 进一步找出城市区号和名称，请在这以后关闭文件
        if ($sign < 0) {
            $this->seek(-$index_size, SEEK_CUR); //回退一条索引
        }
        return $this->readHex($this->term_size); //开头
    }

    /**
     * 查找结尾项
     */
    public function findLineStop()
    {
        if ($this->isStopNearStart()) {
            $curr_stop = $this->readHex($this->term_size); //结尾
            $this->seek($this->readNumber($this->offset_size)); //跳到记录区
        } else {
            $this->seek($this->readNumber($this->offset_size)); //跳到记录区
            $curr_stop = $this->readHex($this->term_size); //结尾
        }
        return $curr_stop;
    }

    /**
     * 报告指针位置（绝对地址）
     */
    public function tell()
    {
        return ftell($this->fp);
    }

    /**
     * 指针跳到某位置
     * $whence: SEEK_SET=绝对 / SEEK_CUR=相对 / SEEK_END=倒数
     */
    public function seek($position, $whence = SEEK_SET)
    {
        if ($whence === SEEK_SET) {
            $position = abs($position);
        } else if ($whence === SEEK_END) {
            $position = -abs($position);
        }
        $result = fseek($this->fp, $position, $whence);
        return $result === 0; //fseek成功时返回0，失败时返回-1
    }

    /**
     * 读取头部信息，前8个字节
     */
    public function readHeaders()
    {
        $this->seek(0);
        $this->index_first = $this->readInt();   #第一条索引区位置，4字节
        $this->index_last = $this->readInt();    #最后一条索引区位置，4字节
        $bytes = $this->index_last - $this->index_first;
        $index_size = $this->getIndexSize();
        if ($index_size > 0) {
            $this->index_total = floor($bytes / $index_size) + 1;
        }
        return $this->index_total;
    }

    /**
     * 读取文件
     */
    public function read($bytes = 1)
    {
        if ($bytes === 1) {
            return fgetc($this->fp);
        } else if ($bytes > 1) {
            return fread($this->fp, $bytes);
        }
    }

    /**
     * 读取若干字节的HEX，一个字节是两个Hex
     */
    public function readHex($bytes = 1)
    {
        $hex = bin2hex($this->read($bytes));
        if (!$this->isBOM() && $bytes > 1) {
            $hex = implode('', array_reverse(str_split($hex, 2)));
        }
        return $hex;
    }

    /**
     * 读取一个整数，若干字节
     */
    public function readNumber($bytes = 3)
    {
        $hex = $this->readHex($bytes);
        return intval($hex, 16);
    }

    /**
     * 读取一个整数，1、2、4、8字节
     */
    public function readInt($bytes = 4)
    {
        $type = 'C';
        if ($bytes === 2) {
            $type = $this->isBOM() ? 'n' : 'v';
        } else if ($bytes === 4) {
            $type = $this->isBOM() ? 'N' : 'V';
        } else if ($bytes === 8) {
            $type = $this->isBOM() ? 'J' : 'P';
        }
        $assoc = unpack($type . 'int', $this->read($bytes));
        return $assoc['int'];
    }

    /**
     * 读取字符串，直到\0或EOF
     */
    public function readString($string = '')
    {
        while (1) {
            $char = $this->read();
            //读到文件结尾EOF或字符串结尾\0
            if ($char === false || ord($char) === 0) {
                break;
            }
            $string .= $char;
        }
        return $string;
    }

    /**
     * 读取第一个非\0字符
     */
    public function firstChar()
    {
        do {
            $char = $this->read();
            if ($char === false) { //文件结束EOF
                return '';
            }
        } while (ord($char) === 0);
        return $char;
    }
}

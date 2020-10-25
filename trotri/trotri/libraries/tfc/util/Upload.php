<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\util;

use tfc\ap\ErrorException;
use tfc\ap\InvalidArgumentException;

/**
 * Upload class file
 * 上传文件类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Upload.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Upload
{
    /**
     * @var integer 文件的命名规则：原文件名
     */
    const NAMERULE_NULL                   = 0;

    /**
     * @var integer 文件的命名规则：随机整数格式
     */
    const NAMERULE_RAND_INTEGER           = 1;

    /**
     * @var integer 文件的命名规则：随机字符串格式
     */
    const NAMERULE_RAND_STRING            = 2;

    /**
     * @var integer 文件的命名规则：日期和时间格式
     */
    const NAMERULE_DATETIME               = 3;

    /**
     * @var integer 文件的命名规则：日期和时间+随机整数格式
     */
    const NAMERULE_DATETIME_RAND_INTEGER  = 4;

    /**
     * @var integer 文件的命名规则：日期和时间+随机字符串格式
     */
    const NAMERULE_DATETIME_RAND_STRING   = 5;

    /**
     * @var integer 文件的命名规则：时间戳格式
     */
    const NAMERULE_TIMESTAMP              = 6;

    /**
     * @var integer 文件的命名规则：时间戳+随机整数格式
     */
    const NAMERULE_TIMESTAMP_RAND_INTEGER = 7;

    /**
     * @var integer 文件的命名规则：时间戳+随机字符串格式
     */
    const NAMERULE_TIMESTAMP_RAND_STRING  = 8;

    /**
     * @var integer OK
     */
    const SUCCESS_NUM             = 0;

    /**
     * @var integer 参数错误
     */
    const ERROR_REQUEST           = 400;

    /**
     * @var integer 上传文件失败：文件大小超过最大限制
     */
    const ERR_ABOVE_MAX_SIZE      = 1;

    /**
     * @var integer 上传文件失败：文件类型不在可允许的范围内
     */
    const ERR_DISALLOW_TYPE       = 2;

    /**
     * @var integer 上传文件失败：文件后缀名不在可允许的范围内
     */
    const ERR_DISALLOW_EXT        = 3;

    /**
     * @var integer 上传文件失败：保存文件的地址已经存在其他文件，并且不允许替换
     */
    const ERR_FILE_ALREADY_EXISTS = 4;

    /**
     * @var integer 上传文件失败：有可能是攻击性质的上传
     */
    const ERR_DISALLOW_UPLOAD     = 5;

    /**
     * @var integer 上传文件失败：将临时文件更新到指定目录失败
     */
    const ERR_MOVE_UPLOADED_FILE  = 6;

    /**
     * @var integer 上传文件失败：未知原因
     */
    const ERR_UPLOADED_FAILED     = 7;

    /**
     * @var string 上传完文件后的完整路径
     */
    protected $_savePath = '';

    /**
     * @var string 上传文件保存目录
     */
    protected $_saveDir = '';

    /**
     * @var string|integer 文件名前缀，可以是用户ID，随机数等唯一标志
     */
    protected $_namePre = '';

    /**
     * @var integer 保存文件时的命名规则
     * 0：原文件名、1：随机整数格式、2：随机字符串格式、3：日期和时间格式、4：日期和时间+随机整数格式
     * 5：日期和时间+随机字符串格式、6：时间戳格式、7：时间戳+随机整数格式、8：时间戳+随机字符串格式
     */
    protected $_nameRule = self::NAMERULE_NULL;

    /**
     * @var integer 允许上传的文件大小最大值，单位：字节
     */
    protected $_maxSize = 2097152;

    /**
     * @var array 允许上传的文件类型
     */
    protected $_allowTypes = array(
        'image/pjpeg',
        'image/jpeg',
        'image/gif',
        'image/png',
        'image/xpng',
        'image/wbmp',
        'image/bmp',
        'image/x-png'
    );

    /**
     * @var array 允许上传的文件后缀
     */
    protected $_allowExts = array('jpg', 'gif', 'png', 'bmp', 'zip', 'rar');

    /**
     * @var boolean 如果保存文件的地址已经存在其他文件，是否允许替换
     */
    protected $_allowReplaceExists = false;

    /**
     * @var string 日期和时间的格式
     */
    protected $_dtFormat = 'YmdHis';

    /**
     * @var string 文件名的连接符
     */
    protected $_joinStr = '_';

    /**
     * @var integer 文件名中随机数取值范围，最小值
     */
    protected $_randMin = 10000;

    /**
     * @var integer 文件名中随机数取值范围，最大值
     */
    protected $_randMax = 99999;

    /**
     * @var integer 文件名中随机字符串长度，取值 8-32
     */
    protected $_randStrLen = 8;

    /**
     * 构造方法：初始化上传文件保存目录、文件的命名规则、保存文件的地址已经存在其他文件，是否允许替换
     * @param string $saveDir
     * @param integer $nameRule
     * @param boolean $allowReplaceExists
     */
    public function __construct($saveDir, $nameRule = self::NAMERULE_NULL, $allowReplaceExists = false)
    {
        $this->setSaveDir($saveDir);
        $this->setNameRule($nameRule);
        $this->setAllowReplaceExists($allowReplaceExists);
    }

    /**
     * 检查并上传文件
     * @param array $files
     * @return string
     * @throws ErrorException 如果参数错误，抛出异常
     * @throws ErrorException 如果上传的文件大小超过限制，抛出异常
     * @throws ErrorException 如果上传的文件类型不在允许列表中，抛出异常
     * @throws ErrorException 如果上传的文件后缀不在允许列表中，抛出异常
     * @throws ErrorException 如果指定的存放地址已经存在其他文件，并且不允许替换其他文件，抛出异常
     * @throws ErrorException 如果可能是攻击性质的上传，抛出异常
     * @throws InvalidArgumentException 如果上传失败，抛出异常
     */
    public function save(array $files)
    {
        if ((!isset($files['size']) && !is_numeric($files['size'])) || !isset($files['type']) || !isset($files['name']) || !isset($files['tmp_name'])) {
            throw new ErrorException('Upload save file failed, Request Error!.', self::ERROR_REQUEST);
        }

        if (!$this->checkSize($files['size'])) {
            throw new ErrorException(sprintf(
                'Upload save file failed, file size "%d" above max size "%d".', $files['size'], $this->getMaxSize()
            ), self::ERR_ABOVE_MAX_SIZE);
        }

        if (!$this->checkType($files['type'])) {
            throw new ErrorException(sprintf(
                'Upload save file failed, file type "%s" disallow, allow types "%s".', $files['type'], implode('|', $this->getAllowTypes())
            ), self::ERR_DISALLOW_TYPE);
        }

        if (!$this->checkExt($files['name'])) {
            throw new ErrorException(sprintf(
                'Upload save file failed, file ext "%s" disallow,, allow exts "%s".', $this->getFileExt($files['name']), implode('|', $this->getAllowExts())
            ), self::ERR_DISALLOW_EXT);
        }

        $this->_savePath = $this->getSaveDir() . DIRECTORY_SEPARATOR . $this->getFileName($files['name']);
        if (!$this->checkSavePath($this->_savePath)) {
            throw new ErrorException(sprintf(
                'Upload save file failed, file "%s" alerady exists but disallow replace.', $this->getSavePath()
            ), self::ERR_FILE_ALREADY_EXISTS);
        }

        if (!$this->checkUpload($files['tmp_name'])) {
            throw new ErrorException(sprintf(
                'Upload save file failed, possible file upload attack, save path "%s", tmp name "%s".', $this->getSavePath(), $files['tmp_name']
            ), self::ERR_DISALLOW_UPLOAD);
        }

        if (!move_uploaded_file($files['tmp_name'], $this->getSavePath())) {
            throw new InvalidArgumentException(sprintf(
                'Upload save file failed, move uploaded file failed, save path "%s", tmp name "%s".', $this->getSavePath(), $files['tmp_name']
            ), self::ERR_MOVE_UPLOADED_FILE);
        }

        if (!is_file($this->getSavePath())) {
            throw new InvalidArgumentException(sprintf(
                'Upload save file failed, save path "%s", tmp name "%s".', $this->getSavePath(), $files['tmp_name']
            ), self::ERR_MOVE_UPLOADED_FILE);
        }

        return $this->getSavePath();
    }

    /**
     * 获取最终保存的文件名
     * @param string $rawFileName
     * @return string
     */
    public function getFileName($rawFileName)
    {
        $fileName = $this->getNamePre();
        switch ($this->getNameRule()) {
            case self::NAMERULE_RAND_INTEGER:
                $fileName .= $this->getRandInteger();
                break;
            case self::NAMERULE_RAND_STRING:
                $fileName .= $this->getRandString();
                break;
            case self::NAMERULE_DATETIME:
                $fileName .= $this->getDateTime();
                break;
            case self::NAMERULE_DATETIME_RAND_INTEGER:
                $fileName .= $this->getDateTime() . $this->getJoinStr() . $this->getRandInteger();
                break;
            case self::NAMERULE_DATETIME_RAND_STRING:
                $fileName .= $this->getDateTime() . $this->getJoinStr() . $this->getRandString();
                break;
            case self::NAMERULE_TIMESTAMP:
                $fileName .= $this->getTimestamp();
                break;
            case self::NAMERULE_TIMESTAMP_RAND_INTEGER:
                $fileName .= $this->getTimestamp() . $this->getJoinStr() . $this->getRandInteger();
                break;
            case self::NAMERULE_TIMESTAMP_RAND_STRING:
                $fileName .= $this->getTimestamp() . $this->getJoinStr() . $this->getRandString();
                break;
            default:
                $fileName .= $this->cleanFileName($rawFileName);
                return $fileName;
        }

        return $fileName . '.' . $this->getFileExt($rawFileName);
    }

    /**
     * 检查上传的文件大小是否超过最大值
     * @param integer $size
     * @return boolean
     */
    public function checkSize($size)
    {
        if ($this->getMaxSize() < 0) {
            return true;
        }

        return $size < $this->getMaxSize();
    }

    /**
     * 检查上传的文件类型是否允许
     * @param string $type
     * @return boolean
     */
    public function checkType($type)
    {
        return in_array(strtolower($type), $this->getAllowTypes());
    }

    /**
     * 检查上传的文件后缀是否允许
     * @param string $fileName
     * @return boolean
     */
    public function checkExt($fileName)
    {
        return in_array($this->getFileExt($fileName), $this->getAllowExts());
    }

    /**
     * 检查上传完文件后的完整路径
     * 果保存文件的地址不存在其他文件，返回True；如果已经存在其他文件并且允许替换其他文件，返回True；否则返回False
     * @param string $savePath
     * @return boolean
     */
    public function checkSavePath($savePath)
    {
        if ($this->getAllowReplaceExists()) {
            return true;
        }

        return !is_file($savePath);
    }

    /**
     * 检查文件是否是通过 HTTP POST上传的，如果不是则可能是攻击性质的上传
     * @param string $fileName
     * @return boolean
     */
    public function checkUpload($fileName)
    {
        return is_uploaded_file($fileName);
    }

    /**
     * 获取上传完文件后的完整路径
     * @return string
     */
    public function getSavePath()
    {
        return $this->_savePath;
    }

    /**
     * 获取上传文件保存目录
     * @return string
     */
    public function getSaveDir()
    {
        return $this->_saveDir;
    }

    /**
     * 设置上传文件保存目录
     * @param string $saveDir
     * @return \tfc\util\Upload
     * @throws ErrorException 如果保存上传文件的目录不存在，抛出异常
     * @throws ErrorException 如果保存上传文件的目录没有可写权限，抛出异常
     */
    public function setSaveDir($saveDir)
    {
        if (!is_dir($saveDir) && !mkdir($saveDir)) {
            throw new ErrorException(sprintf(
                'Upload save dir "%s" is not a valid directory.', $saveDir
            ));
        }

        if (!is_writeable($saveDir)) {
            throw new ErrorException(sprintf(
                'Upload save dir "%s" can not writeable.', $saveDir
            ));
        }

        is_file($saveDir . DS . 'index.html') || file_put_contents($saveDir . DS . 'index.html', '<!DOCTYPE html><title></title>');
        $this->_saveDir = $saveDir;
        return $this;
    }

    /**
     * 获取文件名前缀，可以是用户ID，随机数等唯一标志
     * @return mixed
     */
    public function getNamePre()
    {
        return $this->_namePre;
    }

    /**
     * 设置文件名前缀，可以是用户ID，随机数等唯一标志
     * @param mixed $namePre
     * @return \tfc\util\Upload
     */
    public function setNamePre($namePre)
    {
        $this->_namePre = (string) $namePre;
        return $this;
    }

    /**
     * 获取保存文件时的命名规则
     * 0：原文件名、1：随机整数格式、2：随机字符串格式、3：日期和时间格式、4：日期和时间+随机整数格式
     * 5：日期和时间+随机字符串格式、6：时间戳格式、7：时间戳+随机整数格式、8：时间戳+随机字符串格式
     * @return integer
     */
    public function getNameRule()
    {
        return $this->_nameRule;
    }

    /**
     * 设置保存文件时的命名规则
     * 0：原文件名、1：随机整数格式、2：随机字符串格式、3：日期和时间格式、4：日期和时间+随机整数格式
     * 5：日期和时间+随机字符串格式、6：时间戳格式、7：时间戳+随机整数格式、8：时间戳+随机字符串格式
     * @param integer $nameRule
     * @return \tfc\util\Upload
     * @throws ErrorException 如果参数是无效命名规则，抛出异常
     */
    public function setNameRule($nameRule)
    {
        $nameRule = (int) $nameRule;
        static $nameRules = array(
            self::NAMERULE_NULL,
            self::NAMERULE_RAND_INTEGER,
            self::NAMERULE_RAND_STRING,
            self::NAMERULE_DATETIME,
            self::NAMERULE_DATETIME_RAND_INTEGER,
            self::NAMERULE_DATETIME_RAND_STRING,
            self::NAMERULE_TIMESTAMP,
            self::NAMERULE_TIMESTAMP_RAND_INTEGER,
            self::NAMERULE_TIMESTAMP_RAND_STRING,
        );

        if (in_array($nameRule, $nameRules)) {
            $this->_nameRule = $nameRule;
            return $this;
        }

        throw new ErrorException(sprintf(
            'Upload name rule "%s" provided contains invalid characters, must be integer between 0~8 only.', $nameRule
        ));
    }

    /**
     * 获取允许上传的文件大小最大值
     * @return integer
     */
    public function getMaxSize()
    {
        return $this->_maxSize;
    }

    /**
     * 设置允许上传的文件大小最大值
     * @param integer $maxSize
     * @return \tfc\util\Upload
     */
    public function setMaxSize($maxSize)
    {
        $this->_maxSize = (int) $maxSize;
        return $this;
    }

    /**
     * 获取所有允许上传的文件类型
     * @return array
     */
    public function getAllowTypes()
    {
        return $this->_allowTypes;
    }

    /**
     * 设置所有允许上传的文件类型
     * @param mixed $allowTypes
     * @return \tfc\util\Upload
     * @throws ErrorException 如果参数不是数组或字符串，抛出异常
     */
    public function setAllowTypes($allowTypes)
    {
        if (is_string($allowTypes)) {
            $allowTypes = explode('|', trim($allowTypes, ' |'));
        }

        if (is_array($allowTypes)) {
            $this->_allowTypes = array_map('strtolower', $allowTypes);
            return $this;
        }

        throw new ErrorException(sprintf(
            'Upload set allow type failed, type "%s" must be array or string.', $allowTypes
        ));
    }

    /**
     * 获取所有允许上传的文件后缀
     * @return array
     */
    public function getAllowExts()
    {
        return $this->_allowExts;
    }

    /**
     * 设置所有允许上传的文件后缀
     * @param array $allowExts
     * @return \tfc\util\Upload
     * @throws ErrorException 如果参数不是数组或字符串，抛出异常
     */
    public function setAllowExts($allowExts)
    {
        if (is_string($allowExts)) {
            $allowExts = explode('|', trim($allowExts, ' |'));
        }

        if (is_array($allowExts)) {
            $this->_allowExts = array_map('strtolower', $allowExts);
            return $this;
        }

        throw new ErrorException(sprintf(
            'Upload set allow ext failed, ext "%s" must be array or string.', $allowExts
        ));
    }

    /**
     * 获取如果保存文件的地址已经存在其他文件，是否允许替换
     * @return boolean
     */
    public function getAllowReplaceExists()
    {
        return $this->_allowReplaceExists;
    }

    /**
     * 设置如果保存文件的地址已经存在其他文件，是否允许替换
     * @param boolean $allowReplaceExists
     * @return \tfc\util\Upload
     */
    public function setAllowReplaceExists($allowReplaceExists)
    {
        $this->_allowReplaceExists = (boolean) $allowReplaceExists;
        return $this;
    }

    /**
     * 获取日期和时间的格式
     * @return string
     */
    public function getDtFormat()
    {
        return $this->_dtFormat;
    }

    /**
     * 设置日期和时间的格式
     * @param string $dtFormat
     * @return \tfc\util\Upload
     */
    public function setDtFormat($dtFormat)
    {
        $this->_dtFormat = (string) $dtFormat;
        return $this;
    }

    /**
     * 获取文件名的连接符
     * @return string
     */
    public function getJoinStr()
    {
        return $this->_joinStr;
    }

    /**
     * 设置文件名的连接符
     * @param string $joinStr
     * @return \tfc\util\Upload
     */
    public function setJoinStr($joinStr)
    {
        $this->_joinStr = $joinStr;
        return $this;
    }

    /**
     * 获取文件名中随机数取值范围，最小值
     * @return integer
     */
    public function getRandMin()
    {
        return $this->_randMin;
    }

    /**
     * 设置文件名中随机数取值范围，最小值
     * @param integer $randMin
     * @return \tfc\util\Upload
     */
    public function setRandMin($randMin)
    {
        $this->_randMin = (int) $randMin;
        return $this;
    }

    /**
     * 获取文件名中随机数取值范围，最大值
     * @return integer
     */
    public function getRandMax()
    {
        return $this->_randMax;
    }

    /**
     * 设置文件名中随机数取值范围，最大值
     * @param integer $randMax
     * @return \tfc\util\Upload
     */
    public function setRandMax($randMax)
    {
        $this->_randMax = (int) $randMax;
        return $this;
    }

    /**
     * 获取文件名中随机字符串长度，取值 8-32
     * @return integer
     */
    public function getRandStrLen()
    {
        return $this->_randStrLen;
    }

    /**
     * 设置文件名中随机字符串长度，取值 8-32
     * @param integer $strLen
     * @return \tfc\util\Upload
     */
    public function setRandStrLen($strLen)
    {
        $strLen = min(32, (int) $strLen);
        $strLen = max(8, $strLen);
        $this->_randStrLen = $strLen;
        return $this;
    }

    /**
     * 通过文件名获取文件后缀，文件后缀已转化成小写字符
     * @param string $fileName
     * @return string
     */
    public function getFileExt($fileName)
    {
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        return strtolower($fileExt);
    }

    /**
     * 获取日期和时间
     * @return string
     */
    public function getDateTime()
    {
        return date($this->getDtFormat());
    }

    /**
     * 获取时间戳
     * @return integer
     */
    public function getTimestamp()
    {
        return time();
    }

    /**
     * 获取随机整数
     * @return integer
     */
    public function getRandInteger()
    {
        return mt_rand($this->getRandMin(), $this->getRandMax());
    }

    /**
     * 获取随机字符串
     * @return string
     */
    public function getRandString()
    {
        return substr($this->random(), 0, $this->getRandStrLen());
    }

    /**
     * 获取随机数
     * @return string
     */
    public function random()
    {
        $string = $_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_NAME'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_PORT'].$_SERVER['HTTP_USER_AGENT'].mt_rand().microtime();
        return md5(md5($string) . mt_rand());
    }

    /**
     * 清理文件名
     * @param string $fileName
     * @return string
     */
    public function cleanFileName($fileName)
    {
        $bads = array(
            "<!--", "-->", "<", ">",
            "'", '"', '&', '$', '=', ';', '?', '/',
            "%20", 
            "%22",
            "%3c",        // <
            "%253c",      // <
            "%3e",        // >
            "%0e",        // >
            "%28",        // (
            "%29",        // )
            "%2528",      // (
            "%26",        // &
            "%24",        // $
            "%3f",        // ?
            "%3b",        // ;
            "%3d"         // =
        );

        $fileName = str_replace($bads, '', $fileName);
        return stripslashes($fileName);
    }
}

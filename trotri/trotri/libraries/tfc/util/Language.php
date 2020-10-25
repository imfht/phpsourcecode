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

/**
 * Language class file
 * 语言国际化管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Language.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Language
{
    /**
     * @var string 当没有指定语种时默认的语种
     */
    const DEFAULT_TYPE = 'en-GB';

    /**
     * @var string 语言种类
     */
    protected $_type = self::DEFAULT_TYPE;

    /**
     * @var array 寄存所有的字符串
     */
    protected $_strings = array();

    /**
     * @var string 当前语种的语言包存放目录
     */
    protected $_langDir;

    /**
     * @var string 所有语言包存放目录
     */
    protected $_baseDir;

    /**
     * @var array instances of tfc\util\Language
     */
    protected static $_instances = array();

    /**
     * 构造方法：初始化语种和所有语言包存放目录
     * @param string $type
     * @param string $baseDir
     */
    protected function __construct($type, $baseDir)
    {
        $this->setType($type);
        $this->setBaseDir($baseDir);
    }

    /**
     * 魔术方法：禁止被克隆
     */
    private function __clone()
    {
    }

    /**
     * 单例模式：获取本类的实例
     * @param string $type
     * @param string $baseDir
     * @return \tfc\util\Language
     */
    public static function getInstance($type, $baseDir)
    {
        $key = strtolower($baseDir . '::' . $type);
        if (!isset(self::$_instances[$key])) {
            self::$_instances[$key] = new self($type, $baseDir);
        }

        return self::$_instances[$key];
    }

    /**
     * 通过键名获取语言内容
     * @param string $string
     * @param boolean $jsSafe
     * @param boolean $interpretBackSlashes
     * @return string
     */
    public function _($string, $jsSafe = false, $interpretBackSlashes = true)
    {
        static $search = array('\\\\', '\t', '\n');
        static $replace = array("\\", "\t", "\n");

        $string = strtoupper($string);
        if (!isset($this->_strings[$string])) {
            $fileName = $this->parseFileName($string);
            $this->load($fileName);
        }

        $string = isset($this->_strings[$string]) ? $this->_strings[$string] : '';
        if ($jsSafe) {
            $string = addslashes($string);
        }
        elseif ($interpretBackSlashes) {
            $string = str_replace($search, $replace, $string);
        }

        return $string;
    }

    /**
     * 加载当前语种的ini语言包
     * @param string $fileName
     * @return \tfc\util\Language
     */
    public function load($fileName)
    {
        $fileName = $this->getLangFile($fileName);
        if ($this->fileLoaded($fileName)) {
            return $this;
        }

        $strings = $this->parseIni($fileName);
        $this->_strings = array_merge($this->_strings, $strings);
        return $this;
    }

    /**
     * 解析当前语种的ini语言包
     * @param string $fileName
     * @return array
     */
    public function parse($fileName)
    {
        $fileName = $this->getLangFile($fileName);
        return $this->parseIni($fileName);
    }

    /**
     * 通过语言键名获取该键存放的文件名
     * @param string $string
     * @return string
     */
    public function parseFileName($string)
    {
        $string = substr($string, 0, strpos($string, '_', strpos($string, '_') + 1));
        $fileName = $this->getType() . '.' . strtolower($string) . '.ini';
        return $fileName;
    }

    /**
     * 判断语言文件是否已经被解析过了
     * @param string $fileName
     * @param boolean $addNewLoad
     * @return boolean
     */
    public function fileLoaded($fileName, $addNewLoad = true)
    {
        static $files = array();

        if (isset($files[$fileName])) {
            return true;
        }

        if ($addNewLoad) {
            $files[$fileName] = true;
        }

        return false;
    }

    /**
     * 解析ini文件
     * @param string $fileName
     * @return array
     * @throws ErrorException 如果需要解析的文件不存在，抛出异常
     */
    public function parseIni($fileName)
    {
        if (!is_file($fileName)) {
            throw new ErrorException(sprintf(
                'Language file path "%s" is not a valid file.', $fileName
            ));
        }

        $ret = parse_ini_file($fileName);
        return $ret;
    }

    /**
     * 获取解析过的所有语言串
     * @return array
     */
    public function getStrings()
    {
        return $this->_strings;
    }

    /**
     * 获取当前语种的语言包文件
     * @param string $fileName
     * @return string
     */
    public function getLangFile($fileName)
    {
        $ret = $this->getLangDir() . DIRECTORY_SEPARATOR . $fileName;
        return $ret;
    }

    /**
     * 获取当前语种的语言包存放目录
     * @return string
     */
    public function getLangDir()
    {
        if ($this->_langDir === null) {
            $this->setLangDir();
        }

        return $this->_langDir;
    }

    /**
     * 设置当前语种的语言包存放目录
     * @param string $value
     * @return \tfc\util\Language
     * @throws ErrorException 如果语言包存放的目录不存在，抛出异常
     */
    public function setLangDir($value = null)
    {
        if ($value === null) {
            $value = $this->getBaseDir() . DIRECTORY_SEPARATOR . $this->getType();
        }

        if (!is_dir($value)) {
            throw new ErrorException(sprintf(
                'Language lang dir "%s" is not a valid directory.', $value
            ));
        }

        $this->_langDir = $value;
        return $this;
    }

    /**
     * 获取当前的语言种类
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * 设置当前的语言种类
     * @param string $value
     * @return \tfc\util\Language
     */
    public function setType($value)
    {
        if (($value = trim($value)) !== '') {
            $this->_type = $value;
        }

        return $this;
    }

    /**
     * 获取所有语言包存放目录
     * @return string
     */
    public function getBaseDir()
    {
        return $this->_baseDir;
    }

    /**
     * 设置所有语言包存放目录
     * @param string $value
     * @return \tfc\util\Language
     * @throws ErrorException 如果语言包存放的目录不存在，抛出异常
     */
    public function setBaseDir($value)
    {
        if (!is_dir($value)) {
            throw new ErrorException(sprintf(
                'Language base dir "%s" is not a valid directory.', $value
            ));
        }

        $this->_baseDir = $value;
        return $this;
    }
}

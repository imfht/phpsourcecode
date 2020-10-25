<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\saf;

use tfc\ap\ErrorException;
use tfc\util\Upload;

/**
 * UpProxy class file
 * 上传代理操作类
 *
 * 配置 /cfg/app/appname/main.php：
 * <pre>
 * return array (
 *   'upload' => array(
 *     'posts' => array(
 *       'directory' => 'imgs/thumb', // 上传目录名，在根目录：DIR_DATA_UPLOAD下，如：DIR_DATA_UPLOAD . '/imgs/thumb'
 *       'name_pre' => '',
 *       'name_rule' => 0, // 保存文件时的命名规则，0：原文件名、1：随机整数格式、2：随机字符串格式、3：日期和时间格式、4：日期和时间+随机整数格式、5：日期和时间+随机字符串格式、6：时间戳格式、7：时间戳+随机整数格式、8：时间戳+随机字符串格式
 *       'dir_rule' => 'Ym/d', // 目录名规则，由日期时间组成，如：DIR_DATA_UPLOAD . '/imgs/thumb/201410/04'
 *       'max_size' => 2097152, // 允许上传的文件大小最大值，单位：字节
 *       'allow_types' => array(
 *         'image/pjpeg',
 *         'image/jpeg',
 *         'image/gif',
 *         'image/png',
 *         'image/xpng',
 *         'image/wbmp',
 *         'image/bmp',
 *         'image/x-png'
 *       ),
 *       'allow_exts' => 'jpg|gif|png|bmp|zip|rar',
 *       'allow_replace_exists' => false, // 如果保存文件的地址已经存在其他文件，是否允许替换
 *       'dt_format' => 'YmdHis',
 *       'join_str' => '_',
 *       'rand_min' => 10000,
 *       'rand_max' => 99999,
 *       'rand_strlen' => 16 // 8 ~ 32之间
 *     ),
 *     'sysbatch' => array(
 *       ...
 *     ),
 *   ),
 * )
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: UpProxy.php 1 2013-04-05 01:38:06Z huan.song $
 * @package tfc.saf
 * @since 1.0
 */
class UpProxy
{
    /**
     * @var string 配置名
     */
    const CONFIG_NAME = 'upload';

    /**
     * @var integer OK
     */
    const SUCCESS_NUM             = Upload::SUCCESS_NUM;

    /**
     * @var integer 参数错误
     */
    const ERROR_REQUEST           = Upload::ERROR_REQUEST;

    /**
     * @var integer 上传文件失败：文件大小超过最大限制
     */
    const ERR_ABOVE_MAX_SIZE      = Upload::ERR_ABOVE_MAX_SIZE;

    /**
     * @var integer 上传文件失败：文件类型不在可允许的范围内
     */
    const ERR_DISALLOW_TYPE       = Upload::ERR_DISALLOW_TYPE;

    /**
     * @var integer 上传文件失败：文件后缀名不在可允许的范围内
     */
    const ERR_DISALLOW_EXT        = Upload::ERR_DISALLOW_EXT;

    /**
     * @var integer 上传文件失败：保存文件的地址已经存在其他文件，并且不允许替换
     */
    const ERR_FILE_ALREADY_EXISTS = Upload::ERR_FILE_ALREADY_EXISTS;

    /**
     * @var integer 上传文件失败：有可能是攻击性质的上传
     */
    const ERR_DISALLOW_UPLOAD     = Upload::ERR_DISALLOW_UPLOAD;

    /**
     * @var integer 上传文件失败：将临时文件更新到指定目录失败
     */
    const ERR_MOVE_UPLOADED_FILE  = Upload::ERR_MOVE_UPLOADED_FILE;

    /**
     * @var integer 上传文件失败：未知原因
     */
    const ERR_UPLOADED_FAILED     = Upload::ERR_UPLOADED_FAILED;

    /**
     * @var string 寄存上传配置名
     */
    protected $_clusterName = null;

    /**
     * @var array 寄存上传配置信息
     */
    protected $_config = null;

    /**
     * @var instance of tfc\util\Upload
     */
    protected $_upload = null;

    /**
     * @var string 上传完文件后的完整路径
     */
    protected $_savePath = '';

    /**
     * 构造方法：初始化上传配置名
     * @param string $clusterName
     */
    public function __construct($clusterName)
    {
        $this->_clusterName = $clusterName;
    }

    /**
     * 检查并上传文件
     * @param array $files
     * @return integer
     */
    public function save(array $files)
    {
        $errNo = self::SUCCESS_NUM;

        $upload = $this->getUpload();
        try {
            $upload->save($files);
        }
        catch (\Exception $e) {
            $errNo = $e->getCode();
            $errMsg = $e->getMessage();
            Log::warning($errMsg, $errNo,  __METHOD__);
        }

        return $errNo;
    }

    /**
     * 获取上传文件对象
     * @return \tfc\util\Upload
     */
    public function getUpload()
    {
        if ($this->_upload === null) {
            $config = $this->getConfig();

            $nameRule      = isset($config['name_rule'])            ? (int) $config['name_rule']                : null;
            $replaceExists = isset($config['allow_replace_exists']) ? (boolean) $config['allow_replace_exists'] : null;
            $namePre       = isset($config['name_pre'])             ? trim($config['name_pre'])                 : null;
            $maxSize       = isset($config['max_size'])             ? (int) $config['max_size']                 : null;
            $allowTypes    = isset($config['allow_types'])          ? $config['allow_types']                    : null;
            $allowExts     = isset($config['allow_exts'])           ? trim($config['allow_exts'])               : null;
            $dtFormat      = isset($config['dt_format'])            ? trim($config['dt_format'])                : null;
            $joinStr       = isset($config['join_str'])             ? trim($config['join_str'])                 : null;
            $randMin       = isset($config['rand_min'])             ? (int) $config['rand_min']                 : null;
            $randMax       = isset($config['rand_max'])             ? (int) $config['rand_max']                 : null;
            $randStrlen    = isset($config['rand_strlen'])          ? (int) $config['rand_strlen']              : null;

            $upload = new Upload($this->getDirectory(), $nameRule, $replaceExists);

            $nameRule      === null || $upload->setNameRule($nameRule);
            $replaceExists === null || $upload->setAllowReplaceExists($replaceExists);
            $namePre       === null || $upload->setNamePre($namePre);
            $maxSize       === null || $upload->setMaxSize($maxSize);
            $allowTypes    === null || $upload->setAllowTypes($allowTypes);
            $allowExts     === null || $upload->setAllowExts($allowExts);
            $dtFormat      === null || $upload->setDtFormat($dtFormat);
            $joinStr       === null || $upload->setJoinStr($joinStr);
            $randMin       === null || $upload->setRandMin($randMin);
            $randMax       === null || $upload->setRandMax($randMax);
            $randStrlen    === null || $upload->setRandStrLen($randStrlen);

            $this->_upload = $upload;
        }

        return $this->_upload;
    }

    /**
     * 上传完文件后的完整路径
     * @return string
     */
    public function getSavePath()
    {
        return $this->getUpload()->getSavePath();
    }

    /**
     * 获取上传文件保存目录
     * @return string
     */
    public function getSaveDir()
    {
        return $this->getUpload()->getSaveDir();
    }

    /**
     * 获取允许上传的文件大小最大值
     * @return integer
     */
    public function getMaxSize()
    {
        return $this->getUpload()->getMaxSize();
    }

    /**
     * 获取所有允许上传的文件类型
     * @return array
     */
    public function getAllowTypes()
    {
        return $this->getUpload()->getAllowTypes();
    }

    /**
     * 获取所有允许上传的文件后缀
     * @return array
     */
    public function getAllowExts()
    {
        return $this->getUpload()->getAllowExts();
    }

    /**
     * 获取如果保存文件的地址已经存在其他文件，是否允许替换
     * @return boolean
     */
    public function getAllowReplaceExists()
    {
        return $this->getUpload()->getAllowReplaceExists();
    }

    /**
     * 通过文件名获取文件后缀，文件后缀已转化成小写字符
     * @param string $fileName
     * @return string
     */
    public function getFileExt($fileName)
    {
        return $this->getUpload()->getFileExt($fileName);
    }

    /**
     * 获取上传文件保存目录
     * @return string
     */
    public function getDirectory()
    {
        $directory = $this->getRootDir();
        $dirs = explode('/', date(str_replace('\\', '/', $this->getConfig('dir_rule', ''))));
        foreach ($dirs as $dirName) {
            $directory .= DS . $dirName;
            $this->mkDir($directory);
        }

        return $directory;
    }

    /**
     * 获取上传文件保存根目录，如：DIR_DATA_UPLOAD . '/imgs/thumb'
     * @return string
     */
    public function getRootDir()
    {
        $directory = DIR_DATA_UPLOAD;
        $dirs = explode('/', str_replace('\\', '/', $this->getConfig('directory', '')));
        foreach ($dirs as $dirName) {
            $directory .= DS . $dirName;
            $this->mkDir($directory);
        }

        return $directory;
    }

    /**
     * 获取上传配置信息
     * @param mixed $key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if ($this->_config === null) {
            $config = Cfg::getApp($this->getClusterName(), self::CONFIG_NAME);
            $this->_config = $config;
        }

        if ($key === null) {
            return $this->_config;
        }

        return isset($this->_config[$key]) ? $this->_config[$key] : null;
    }

    /**
     * 获取上传配置名
     * @return string
     */
    public function getClusterName()
    {
        return $this->_clusterName;
    }

    /**
     * 新建目录
     * @param string $directory
     * @throws ErrorException 如果保存上传文件的目录不存在，抛出异常
     * @throws ErrorException 如果保存上传文件的目录没有可写权限，抛出异常
     * @return \tfc\saf\UpProxy
     */
    public function mkDir($directory)
    {
        if (!is_dir($directory) && !mkdir($directory)) {
            throw new ErrorException(sprintf(
                'UpProxy mkdir "%s" is not a valid directory.', $directory
            ));
        }

        if (!is_writeable($directory)) {
            throw new ErrorException(sprintf(
                'UpProxy mkdir dir "%s" can not writeable.', $directory
            ));
        }

        is_file($directory . DS . 'index.html') || file_put_contents($directory . DS . 'index.html', '<!DOCTYPE html><title></title>');
        return $this;
    }
}

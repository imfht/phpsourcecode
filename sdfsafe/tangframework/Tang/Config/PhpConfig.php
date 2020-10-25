<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Config;
use Tang\Exception\SystemException;
/**
 * 修改版array_replace_recursive
 * @param array $array1
 * @param array $array2
 * @return array
 */
function arrayReplaceRecursive(array $array1,array $array2)
{
    $array = array_replace_recursive($array1,$array2);
    foreach($array as $key => $value)
    {
        if(isset($array1[$key]))
        {
            if(is_array($array1[$key]) && is_array($value))
            {
                if($array1[$key] == $array2[$key])
                {

                } else if(is_array($array2))
                {
                    $array[$key] = arrayReplaceRecursive($array1[$key],$array2[$key]);
                } else
                {
                    $array[$key] = $array1[$key];
                }
            } else if(!$value)
            {
                $array[$key] = $array1[$key];
            }
        }
    }
    return $array;
}

/**
 * PHP配置文件类
 * Class PhpConfig
 * @package Tang\Config
 */
class PhpConfig implements IConfig
{
    /**
     * 配置数据
     * @var array
     */
    private $_data = [];
    private $_replaceData =[
        'debug' => true,//是否开启调试模式 不在调试模式下将屏蔽错误，缓存模板
        'timezone' => 'Asia/Chongqing', //时区设置
        'lang' => 'Zh-cn',//默认的语言
        'charset' => 'UTF-8',//编码
        'dataDirctory' => 'Data',//缓存 等数据目录
        '404Page' => '404.html',//404页面 开发者自定义404可放置在网站目录Lib/Pages里面
        'messagePage' => 'Message.html',//消息提示页面 开发者自定义消息提示页面可放置在网站目录Lib/Pages里面
        'exceptionPage' => 'Exception.html',//异常提示页面 开发者自定义异常提示可放置在网站目录Lib/Pages里面
        'services' => [],
        //ajax配置
        'ajax' => ['requestName' => 'ajaxType', 'callback' => 'callback']
    ];
    /**
     * 项目路径
     * @var string
     */
    private $applicationDirectory = '';

    /**
     * @see IConfig::setApplicationDirectory
     */
    public function setApplicationDirectory($directory)
    {
        $this->applicationDirectory = $directory;
    }
    /**
     * @see IConfig::getApplicationDirectory
     */
    public function getApplicationDirectory()
    {
        return $this->applicationDirectory;
    }
    /**
     * 获取$key的配置内容，如果不存在的话则返回$defautValue
     * @param string $key
     * @param string $defautValue
     * @return string
     */
    public function get($key,$defautValue='')
    {
        $name = '';
        $this->getConfigNameAndValueName($key,$name);
        if(!isset($this->_data[$name]))
        {
            $this->load($name);
        }
        if($key == '*')
        {
            return $this->_data[$name];
        } else if (isset($this->_data[$name][$key]))
        {
            return $this->_data[$name][$key];
        } else
        {
            return $defautValue;
        }
    }

    /**
     * 根据$key获取配置内容，并且根据$replaceData进行替换
     * @param $key
     * @param array $replaceData
     * @return array|string
     */
    public function replaceGet($key,array $replaceData)
    {
        $config = $this->get($key);
        if(!is_array($config))
        {
            return $replaceData;
        }
        return arrayReplaceRecursive($replaceData,$config);
    }

    /**
     * 设置一个配置文件值
     * @param string $key
     * @param mixed $value
     */
    public function set($key,$value)
    {
        $name = '';
        $this->getConfigNameAndValueName($key,$name);
        if($key == '*')
        {
            $this->_data[$name] = $value;
        } else
        {
            $this->_data[$name][$key] = $value;
        }
    }

    /**
     * 保存所有配置
     */
    public function saveAll()
    {
        foreach ($this->_data as $name => $value)
        {
            $this->save($name);
        }
    }

    /**
     * 保存一个配置文件，如果不存在数据的话。$isCreate为true的时候则创建一个配置文件
     * @param string $name
     * @param bool $isCreate
     * @throws \Tang\Exception\SystemException
     */
    public function save($name,$isCreate=false)
    {
        $filePath = $this->getConfigPath($name);
        if(!isset($this->_data[$name]) && !file_exists($filePath))
        {
            if($isCreate)
            {
                file_put_contents($filePath, '<?php return [];');
            } else
            {
                throw new SystemException('Save the configuration file contains no data!',null,10000,'EMERG');
            }
        } else
        {
            file_put_contents($filePath,'<?php return '.var_export($this->_data[$name],true).';');
        }
    }

    /**
     * 创建一个配置文件
     * @param string $name
     */
    public function create($name)
    {
        return $this->save($name,true);
    }

    /**
     * 加载
     * @param $name
     * @throws \Tang\Exception\SystemException
     */
    protected function load($name)
    {
        $filePath = $this->getConfigPath($name);
        $isApplicationConfigFile = $name == 'application';
        if($isApplicationConfigFile && !isset($this->_data[$name]))
        {
            $this->_data[$name] = $this->_replaceData;
        }
        if(!$filePath || !file_exists($filePath))
        {
            throw new SystemException('Configuration file [%s] does not exist!',[$filePath],10001,'EMERG');
        }
        $temp = include $filePath;
        if(!$temp || !is_array($temp))
        {
            throw new SystemException('Configuration file failed to load [%s], please check whether the returned array!',[$filePath],10002,'EMERG');
        } else if($isApplicationConfigFile)
        {
            $this->_data[$name] = arrayReplaceRecursive($this->_replaceData,$temp);
        } else
        {
            $this->_data[$name] = $temp;
        }
    }

    /**
     * 获取配置文件路径
     * @param $name
     * @return string
     */
    protected function getConfigPath($name)
    {
        return sprintf('%sLib%sConfig%s%s.php',$this->applicationDirectory,DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,ucfirst($name));
    }

    protected function getConfigNameAndValueName(&$key,&$name)
    {
        $index = strpos($key,'.');
        if($index)
        {
            $name = substr($key,0,$index);
            $key = substr($key, $index+1);
        } else
        {
            $name = 'application';
        }
    }
    private function __clone()
    {
    }
}
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
namespace Tang\Storage\Drivers;
use Tang\Exception\SystemException;

/**
 * 存储基类
 * Class Base
 * @package Tang\Storage\Drivers
 */
abstract class Base
{
    /**
     * 驱动名称
     * @var string
     */
    protected $name = 'local';
    /**
     * 存储空间配置
     * @var array
     */
    protected $buckets = array();
    /**
     * 当前的存储空间
     * @var string
     */
    protected $bucket = '';
    /**
     * 配置
     * @var array
     */
    protected $config;

    /**
     * 设置配置
     * @param array $config
     * @throws \Tang\Exception\SystemException
     */
    public function setConfig(array $config)
    {
        $buckets = array();
        if(!$config['buckets'] || !is_array($config['buckets']))
        {
            throw new SystemException('not buckets');
        }
        $this->config = $config;
        foreach($config['buckets'] as $key => $value)
        {
            $value = $this->checkBucket($key,$value);
            $value['bucket'] = $key;
            $buckets[$key] = $value;
        }
        $this->buckets = $buckets;
        $this->setBucket(key($buckets));
    }

    /**
     * 获取文件句柄
     * @param $file
     * @param $mode
     * @return resource
     * @throws \Tang\Exception\SystemException
     */
    public function getFileHandle($file,$mode = 'r')
    {
        if(is_resource($file))
        {
            return $file;
        }else if(!file_exists($file))
        {
            throw new SystemException('[%s] is not exists',$file);
        }
        $fp = fopen($file,$mode);
        if(!$fp)
        {
            throw new SystemException('[%s] is not exists',$file);
        }
        return $fp;
    }

    /**
     * 设置存储空间
     * @param $bucket
     * @throws \Tang\Exception\SystemException
     */
    public function setBucket($bucket)
    {
        if(!isset($this->buckets[$bucket]))
        {
            throw new SystemException('buckets not found');
        }
        if($this->bucket)
        {
            if($this->bucket['bucket'] == $bucket)
            {
                return;
            } else
            {
                $this->buckets[$this->bucket['bucket']] = $this->bucket;
            }
        }
        $this->bucket = $this->buckets[$bucket];
    }

    /**
     * 获取当前的存储
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * 获取绝对路径
     * @param $path
     * @return string
     */
    public function getAbsolutePath($path)
    {
        $path = $this->trim($path);
        $path = $this->bucket['directory'].$path;
        return $path;
    }

    /**
     * trim
     * @param $path
     * @return string
     */
    protected function trim($path)
    {
        return trim($path,'/\\');
    }

    /**
     * 获取文件结构数组
     * @param $file
     * @return array
     */
    protected function getFileStruct($file)
    {
        return array('driver' => $this->name,'file' => $file,'bucket' => $this->bucket['bucket'],'url'=>$this->getUrl($file));
    }

    /**
     * 检查存储空间配置
     * @param $name bucket name
     * @param $bucket 配置
     * @return mixed
     */
    protected abstract function checkBucket($name,$bucket);

    /**
     * 根据$file获取外部访问URL地址
     * @param $file
     * @return mixed
     */
    protected abstract function getUrl($file);
}
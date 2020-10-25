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
namespace Tang\GD;
/**
 * 图形类
 * Class Image
 * @package Tang\GD
 */
class Image
{
    /**
     * 图形资源
     * @var \Tang\GD\Resource
     */
    protected $resource;
    /**
     * 输出文件名
     * @var string
     */
    protected $outFile;
    /**
     * @param \Tang\GD\Resource $resource 图片资源
     * @param string $outFile 输出文件
     */
    public function __construct(Resource $resource,$outFile = '')
    {
        $this->outFile = $outFile;
        $this->setResource($resource);
    }

    /**
     * 设置图形资源
     * @param \Tang\GD\Resource $resource
     */
    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * 获取图形资源
     * @return \Tang\GD\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }
    /**
     * 传入绘制对象进行绘制
     * 返回绘制对象返回的值
     * @param IDraw $draw
     * @return mixed
     */
    public function draw(IDraw $draw)
    {
        return $draw->draw($this);
    }

    /**
     * 浏览器输出
     */
    public function browseImage()
    {
        $this->resource->browseImage();
    }

    /**
     * 输出文件
     * 如果有outFile则保存到outFile
     * 否则输出到浏览器
     */
    public function makeFile()
    {
        if($this->outFile)
            $this->resource->makeFile($this->outFile);
        else
            $this->resource->browseImage();
    }
}
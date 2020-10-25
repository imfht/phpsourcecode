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
namespace Tang\Log\Drivers;
use Tang\IO\Interfaces\IFile;
use Tang\Log\ILoger;

/**
 * 文件日志实现
 * Class File
 * @package Tang\Log\Drivers
 */
class File implements ILoger
{
    /**
     * 文件IO对象
     * @var \Tang\IO\Interfaces\IFile
     */
    protected $file;

    /** 文件地址格式
     * @var string
     */
    protected $filePath;

    /**
     * @param IFile $file 文件IO
     * @param array $config 配置
     */
    public function __construct(IFile $file,array $config)
    {
        $this->file = $file;
        $config['filePath'] = trim($config['filePath'],'/\\');
        $this->filePath = $config['dataDirctory'].ucfirst($config['directory']).DIRECTORY_SEPARATOR.$config['filePath'];
    }

    /**
     * @see ILoger::write
     */
    public function write($message,$level)
    {
        $message = date('Y-m-d H:i:s ').$level.':'.$message.PHP_EOL;
        $this->file->append(strftime($this->filePath),$message);
    }
}
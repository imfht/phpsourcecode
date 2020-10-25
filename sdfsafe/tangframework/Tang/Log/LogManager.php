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
namespace Tang\Log;
use Tang\Database\Sql\DB;
use Tang\Log\Drivers\Database;
use Tang\Log\Drivers\File;
use Tang\Manager\Manager;
use Tang\Services\FileService;

/**
 * 日志管理器
 * Class LogManager
 * @package Tang\Log
 */
class LogManager extends Manager implements ILogManager
{
    /**
     * 创建文件日志驱动
     * @return File
     */
    public function createFileDriver()
    {
        return new File(FileService::getService(),$this->config['file']);
    }

    /**
     * 创建数据库驱动
     * @return Database
     */
    public function createDatabaseDriver()
    {
        $config = $this->config['database'];
        return new Database(DB::get($config['source'])->table($config['tableName']));
    }

    /**
     * 默认的日志引擎使用的
     * @param $message
     * @param int $level
     * @return mixed
     */
    public function write($message,$level)
    {
        if(!$this->config['record'] || ($this->config['recordLevel'] && !in_array($level,$this->config['recordLevel'])))
        {
            return;
        }
        return $this->driver()->write($message,$level);
    }

    /**
     * @param string $name
     * @return ILoger
     */
    public function driver($name = '')
    {
        return parent::driver($name);
    }

    /**
     * @see Manager::getIntreface
     */
    protected function getIntreface()
    {
        return '\Tang\Log\ILoger';
    }
}
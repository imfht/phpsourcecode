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
use Tang\Database\Sql\Query\Builder;
use Tang\Log\ILoger;

/**
 * 数据库日志实现
 * 需要用户自行建表
 * 包含字段为
 * id(主键) message level time
 * @package Tang\Log\Drivers
 */
class Database implements ILoger
{
    /**
     * @var Builder
     */
    protected $query;

    /**
     * 数据库构建器
     * @param Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * @see ILoger::write
     */
    public function write($message,$level)
    {
        $this->query->insert(array(
            'message' => $message,
            'level' => $level,
            'time' => time()
        ));
    }
}
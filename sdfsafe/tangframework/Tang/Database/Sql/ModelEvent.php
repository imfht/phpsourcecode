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
namespace Tang\Database\Sql;
use Tang\Event\IEvent;

/**
 * 模型事件
 * @package Tang\Database\Sql
 */
class ModelEvent
{
    /**
     * 事件对象
     * @var \Tang\Event\IEvent
     */
    protected $event;
    public function __construct(IEvent $event)
    {
        $this->event = $event;
    }

    /**
     * 增加添加前事件
     * @param callable $handler
     */
    public function addBeforeInsert($handler)
    {
        $this->event->addListener('beforInsert',$handler);
    }

    /**
     * 添加完事件
     * @param callable $handler
     */
    public function addEndInsert($handler)
    {
        $this->event->addListener('endInsert',$handler);
    }

    /**
     * 添加更新前事件
     * @param callable $handler
     */
    public function addBeforeUpdate($handler)
    {
        $this->event->addListener('beforUpdate',$handler);
    }

    /**
     * 添加更新后事件
     * @param callable $handler
     */
    public function addEndUpdate($handler)
    {
        $this->event->addListener('endUpdate',$handler);
    }

    /**
     * 增加删除前处理事件
     * @param callable $handler
     */
    public function addBeforeDelete($handler)
    {
        $this->event->addListener('beforDelete',$handler);
    }

    /**
     * 增加删除后处理事件
     * @param callable $handler
     */
    public function addEndDelete($handler)
    {
        $this->event->addListener('endDelete',$handler);
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method,$parameters)
    {
        return call_user_func_array(array($this->event,$method),$parameters);
    }
}
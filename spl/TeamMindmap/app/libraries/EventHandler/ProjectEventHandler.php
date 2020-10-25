<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-9
 * Time: 下午3:02
 */

namespace Libraries\EventHandler;

use Auth;
use Libraries\NotificationService\ProjectNotificationHandler;

/**
 * 项目事件处理器
 * Class ProjectEventHandler
 * @package Libraries\EventHandler
 */
class ProjectEventHandler extends AbstractEventHandler
{
    /**
     * 对应事件：eloquent.created :Project的处理方法
     * 消息的接受对象：项目的创建者和全体成员
     *
     * @param $event
     */
    public function projectUpdated($event)
    {
        $title = $event['name'];
        $content = Auth::user()['username']. '更新了项目';

        ProjectNotificationHandler::getInstance()->handleEvent($event, $title, $content);
    }

    /**
     * 对应事件：eloquent.deleting :Project的处理方法
     * 消息的接受对象：项目的创建者和全体成员
     *
     * @param $event
     */
    public function projectDestroyed($event)
    {
        $title = $event['name'];
        $content = Auth::user()['username']. '删除了项目';

        ProjectNotificationHandler::getInstance()->handleEvent($event, $title, $content);
    }

    /**
     * 配置事件订阅者所监听的事件和对应的处理方法
     */
    protected function config()
    {
        $eloquentAction = [
            'updated' => '@projectUpdated',
            'deleting' => '@projectDestroyed'
        ];
        $this->setEloquentEvent($eloquentAction, 'Project');
    }


    protected $handlerName = 'ProjectEventHandler';

}

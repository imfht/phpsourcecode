<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-10
 * Time: 上午12:15
 */

namespace Libraries\EventHandler;

use Auth;
use Libraries\NotificationService\TaskNotificationHandler;

/**
 * 任务事件处理器
 * Class TaskEventHandler
 * @package Libraries\EventHandler
 */
class TaskEventHandler extends AbstractEventHandler
{
    /**
     * 对应事件：eloquent.created: ProjectTask的处理方法
     * 消息的接受对象：项目的创建者和全体成员
     *
     * @param $event
     */
    public function taskCreated($event)
    {
        $title = $event['name'];
        $content = Auth::user()['username']. '创建了新任务';

        TaskNotificationHandler::getInstance()->handleEvent($event, $title, $content);
    }

    /**
     * 对应事件：eloquent.updated: ProjectTask的处理方法
     * 消息的接受对象：项目的创建者和全体成员
     *
     * @param $event
     */
    public function taskUpdated($event)
    {
        $title = $event['name'];
        $content = Auth::user()['username']. '更新了任务';

        TaskNotificationHandler::getInstance()->handleEvent($event, $title, $content);
    }

    /**
     * 对应事件：eloquent.deleting: ProjectTask的处理方法
     * 消息的接受对象：项目的创建者和全体成员
     *
     * @param $event
     */
    public function taskDestroyed($event)
    {
        $title = $event['name'];
        $content = Auth::user()['username']. '删除了任务';

        TaskNotificationHandler::getInstance()->handleEvent($event, $title, $content);
    }

    /**
     * 配置事件订阅者所监听的事件和对应的处理方法
     */
    protected function config()
    {
        $eloquentAction = [
            'created' => '@taskCreated',
            'updated' => '@taskUpdated',
            'deleting' => '@taskDestroyed'
        ];
        $this->setEloquentEvent($eloquentAction, 'ProjectTask');
    }


    protected $handlerName = 'TaskEventHandler';

}

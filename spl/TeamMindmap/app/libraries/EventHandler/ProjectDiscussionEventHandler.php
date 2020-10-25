<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-25
 * Time: 下午1:47
 */
namespace Libraries\EventHandler;

use Auth;
use Libraries\NotificationService\ProjectDiscussionNotificationHandler;
/**
 * 项目讨论事件处理器
 * Class ProjectDiscussionEventHandler
 * @package Libraries\EventHandler
 */
class ProjectDiscussionEventHandler extends AbstractEventHandler
{

    /**
     * 新建讨论触发通知创建、通知发送
     * @param $event
     */
    public function discussionCreated($event)
    {
        $title = $event['title'];
        $content = Auth::user()['username']. ' 新建了讨论-'. $event['title'];

        ProjectDiscussionNotificationHandler::getInstance()->handleEvent($event, $title, $content);
    }

    /**
     * 更新讨论触发通知创建、通知发送
     * @param $event
     */
    public function discussionUpdated($event)
    {
        $title = $event['title'];
        $content = Auth::user()['username']. ' 更新了讨论-'. $event['title'];

        ProjectDiscussionNotificationHandler::getInstance()->handleEvent($event, $title, $content);
    }

    /**
     * 指派项目成员关注讨论触发通知创建、通知发送
     * @param $event
     */
    public function followerAdd($event)
    {
        $title = $event['title'];
        $content = Auth::user()['username']. ' 请求你关注讨论-'. $event['title'];

        ProjectDiscussionNotificationHandler::getInstance()->handleEvent($event, $title, $content);
    }

    protected function config()
    {
        $eloquentAction = [
            'created' => '@discussionCreated',
            'updated' => '@discussionUpdated'
        ];
        $this->setEloquentEvent($eloquentAction, 'ProjectDiscussion');
        $this->setEvent('addFollower', $this->handlerName. '@followerAdd');
    }


    protected $handlerName = 'ProjectDiscussionEventHandler';
}

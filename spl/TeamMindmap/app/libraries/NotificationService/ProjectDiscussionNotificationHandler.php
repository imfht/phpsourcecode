<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-3-10
 * Time: 下午5:22
 */

namespace Libraries\NotificationService;

/**
 * Class ProjectDiscussionNotificationHandler
 */
class ProjectDiscussionNotificationHandler extends AbstractNotificationsService
{
    /**
     * 针对特定事件进行处理
     * @param $event
     * @param $title
     * @param $content
     */
    public function handleEvent($event, $title, $content)
    {
        $newNotify = $this->createNotification($event, $title, $content, static::$notificationType);
        $this->sendNotification($event['project_id'], $newNotify['id']);
    }

    /**
     * 根据数据库约定项目讨论相关通知类型为4
     * @var int
     */
    private static $notificationType = 4;
}
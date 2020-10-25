<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-29
 * Time: 下午3:22
 */

namespace Libraries\EventHandler;

use Auth;
use Libraries\NotificationService\ProjectSharingNotificationHandler;

/**
 * 项目分享事件处理器
 * Class ProjectSharingEventHandler
 * @package Libraries\EventHandler
 */
class ProjectSharingEventHandler extends AbstractEventHandler
{

    protected function config()
    {
        $eloquentAction = [
            'created' => '@sharingCreated'
        ];
        $this->setEloquentEvent($eloquentAction, 'ProjectSharing');
    }

    /**
     * 分享创建时触发通知创建、通知发送
     * @param $event
     */
    public function sharingCreated($event)
    {
        $title = $event['name'];
        $content = Auth::user()['username']. ' 新建了分享'. $event['title'];

        ProjectSharingNotificationHandler::getInstance()->handleEvent($event, $title, $content);
    }



    protected $handlerName = 'ProjectSharingEventHandler';
}

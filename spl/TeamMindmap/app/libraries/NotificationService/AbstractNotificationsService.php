<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-3-10
 * Time: 下午5:09
 */

namespace Libraries\NotificationService;

use Notification;
use Project;
use Project_Member;
use NotifyInbox;
use Illuminate\Database\Eloquent;
use Auth;

/**
 * 通知服务抽象类，为相关事件提供一组内聚的通知服务。处理业务包括
 * 创建通知、发送通知
 * Class AbstractNotificationsService
 */
abstract class AbstractNotificationsService
{

    /**
     * @param $event array 消息源
     * @param $title  string 通知的标题
     * @param $content string 通知的内容
     * @param $notifyType int 通知的类型
     * @return Illuminate\Database\Eloquent\Model 新创建的通知模型
     */
    protected function createNotification($event, $title, $content, $notifyType)
    {
        $notifyData['type_id'] = $notifyType;
        $notifyData['trigger_id'] = Auth::user()['id'];
        $notifyData['source_id'] = $event['id'];
        $notifyData['project_id'] = $event['project_id'];

        $notifyData['title'] = $title;
        $notifyData['content'] = $content;

        $notifyData['created_at'] = date('Y-m-d');
        $notifyData['updated_at'] = date('Y-m-d');

        return Notification::create($notifyData);

    }


    /**
     * 给接受者发送通知
     *
     * @param $sourceId int 事件源的id
     * @param $notificationId int 通知的id
     */
    protected function sendNotification($sourceId, $notificationId)
    {

        $receiverIds = array_fetch(
            Project_Member::where('project_id', $sourceId)->get(['member_id'])->toArray(),
            'member_id'
        );

        array_push($receiverIds, Project::findOrFail($sourceId)['creater_id']);

        $this->storeToInbox($notificationId, $receiverIds);
    }


    /**
     * 给接收者存储通知
     *
     * @param $notificationId int 要存储的通知的id
     * @param $receiverIds array 通知的接收者
     */
    protected function storeToInbox($notificationId, $receiverIds = array())
    {
        foreach ( $receiverIds as $receiverId ) {
            NotifyInbox::create( ['notification_id' => $notificationId, 'receiver_id' => $receiverId ]);
        }
    }

    /**
     * 根据上下文获取当前类实例
     * @return static
     */
    public  static function getInstance()
    {
        return new static();
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-4
 * Time: 下午4:10
 */
class Notification extends Eloquent
{
    /**
     * 用于获取用户的所有通知
     *
     * @param $userId int 用户的id
     * @param $read bool 筛选条件，是否已读
     * @return array 关联数组，存储用户的通知（包括通知的触发者的详细信息）
     */
    public static function getUserNotify($userId, $read)
    {
        $notifyBuilder = Notification::getBaseBuilder($userId)->where('read', $read);
        return Paginate::paginateBuilder( $notifyBuilder )->toArray();
    }

    /**
     * 用于获取用户的某一项目的所有通知（包括其所有任务的通知）
     *
     * @param $userId int 用户的id
     * @param $projectId int 项目的id
     * @param $read bool 筛选条件，是否已读
     * @return array 关联数组，存储通知信息（包括通知的触发者的详细信息）
     */
    public static function getNotifyBelongsToProject($userId, $projectId, $read)
    {
        $notifyBuilder = Notification::getBaseBuilder($userId)
            ->where('project_id', $projectId)
            ->where('read', $read)
            ->whereIn('type_id', [2, 3, 4, 5]);

        return Paginate::paginateBuilder( $notifyBuilder )->toArray();
    }

    /**
     * 用于获取用户的一个或多个项目的通知
     *
     * @param $userId int 用户的id
     * @param $projectId int|array 项目的id（可多个）
     * @param $read bool 筛选条件，是否已读
     * @return array 关联数组，存储通知信息（包括通知的触发者的详细信息）
     */
    public static function getProjectNotify($userId, $projectId, $read)
    {
        $typeId = 2;

        $projectBuilder = Notification::getSpecifiedBuilder($userId, $typeId, $projectId)->where('read', $read);
        return Paginate::paginateBuilder( $projectBuilder )->toArray();
    }

    /**
     * 用于获取用户的某一项目下的所有任务的通知
     *
     * @param $userId int 用户的id
     * @param $projectId int 项目的id
     * @param $read bool 筛选条件，是否已读
     * @return array 关联数组，存储通知信息（包括通知的触发者的详细信息）
     */
    public static function getProjectTaskNotify($userId, $projectId, $read)
    {
        $typeId = 3;

        $projectTaskBuilder =  Notification::getSpecifiedBuilder($userId, $typeId, $projectId)->where('read', $read);
        return Paginate::paginateBuilder( $projectTaskBuilder )->toArray();
    }

    /**
     * 根据某个type_id，project_id来构造一个查询构造器
     * 
     * @param $userId int 用户的id
     * @param $typeId int 通知的类型id
     * @param $projectId int 事件源的id
     * @return \Illuminate\Database\Eloquent\Builder 已根据相关参数构造好的查询构造器
     */
    public static function getSpecifiedBuilder($userId, $typeId, $projectId)
    {
        return Notification::getBaseBuilder($userId)
            ->where('type_id', $typeId)
            ->where('project_id', $projectId);
    }

    /**
     * 返回触发者的User Model对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function trigger()
    {
        return $this->hasOne('User', 'id', 'trigger_id');
    }

    /**
     * 返回接收者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function receivers()
    {
        return $this->belongsToMany('User', 'notifyInbox', 'notification_id', 'receiver_id');
    }

    /**
     * 获取通知的事件源的详细信息
     *
     * @return $sourceData array 关联数组，存储事件源的详细信息
     */
    public function getSourceData()
    {
        $typeId = $this->attributes['type_id'];
        $sourceId = $this->attributes['source_id'];

        $currNotifyType = NotifyType::find($typeId)->toArray();
        $sourceData[$currNotifyType['name']] = $currNotifyType['map']::find($sourceId)->toArray();

        return $sourceData;
    }

    /**
     * 获取基本的查询构造器，构造出一张包括用户的所有通知记录和相关字段的数据表单
     *
     * @param $userId int 用户的id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function getBaseBuilder($userId)
    {
        return Notification::orderBy('notifications.created_at', 'desc')
                        ->with('trigger')
                        ->leftJoin('notifyInbox', "notifications.id", '=', "notifyInbox.notification_id")
                        ->where('receiver_id', $userId);
    }

    protected $table = 'notifications';

    protected $guarded = ['id'];

}

<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-4
 * Time: 下午7:50
 */
class NotifyInbox extends Eloquent
{

    /**
     * 某一用户阅读了某一条通知
     *
     * @param $userId int 阅读通知的用户id
     * @param $notifyId int 通知的id
     * @return int|null 返回更新的记录数目，失败时返回null
     */
    public static function readNotify($userId, $notifyId){
        $currNotify = NotifyInbox::locateNotify($userId, $notifyId);
        return $currNotify->update(['read' => true]);
    }

    /**
     * 删除某一用户与某一条通知的关联
     *
     * @param $userId int 用户的id
     * @param $notifyId int 通知的id
     * @return int 返回删除的记录数目
     */
    public static function deleteNotify($userId, $notifyId){
        $currNotify = NotifyInbox::locateNotify($userId, $notifyId);
        return $currNotify->delete();
    }

    /**
     * 返回某用户未读通知的统计信息
     *
     * @param $userId 待查询的用户id
     * @return int 统计数目
     */
    public static function getUnreadStatistics($userId)
    {
        return static::where('receiver_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * 根据用户id和通知id来进行定位
     *
　   * @param $userId int 用户的id
     * @param $notifyId int 通知的id
     * @return model 返回在NotifyInbox中定位到的一条记录
     */
    private static function locateNotify($userId, $notifyId){
        return NotifyInbox::where('id', $notifyId)->where('receiver_id', $userId);
    }

    protected $table = 'notifyInbox';

    protected $guarded = ['id'];
}

<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-11
 * Time: 上午9:52
 */

class MessageInbox extends Eloquent
{
    /**
     * 获取某一用户接收的信息.
     *
     * @param $usrId 待查询的用户的id
     * @param string $orderType 信息的排序方法，默认是`desc`， 即最近发送的排在前面
     * @return array|static[]
     */
    public static function getUserMessages($usrId, $orderType = 'desc')
    {
        return DB::table('messagesInboxs')
            ->where('receiver_id', $usrId)
            ->leftJoin('messages', 'messagesInboxs.message_id', '=', 'messages.id')
            ->leftJoin('users', 'messages.sender_id', '=', 'users.id')
            ->select(
                'messagesInboxs.id AS id',
                'messages.title AS title',
                'messages.content AS content',
                'messages.sender_id AS sender_id',
                'messages.created_at AS created_at',
                'messagesInboxs.read AS read',
                'users.username AS sender_username',
                'users.email AS sender_email',
                'users.head_image AS sender_head_image'
            )
            ->orderBy('created_at', $orderType)
            ->get();
    }

    /**
     * 返回指定用户的未读消息统计
     *
     * @param $userId 待查询的用户id
     * @return int  未读私信的统计数字
     */
    public static function getUnreadStatistics($userId)
    {
        return static::where('receiver_id', $userId)
            ->where('read', false)
            ->count();
    }

    protected $guarded = ['id'];

    protected $table = 'messagesInboxs';
}
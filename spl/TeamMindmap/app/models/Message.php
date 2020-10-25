<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-11
 * Time: 上午9:41
 */

/**
 * Class Message
 * 私信发送模型
 */
class Message extends Eloquent
{
    /**
     * 获取某一个用户所曾经发送过的信息.
     *
     * @param $userId   待查询的用户id
     * @param string $orderType 排序的类型，默认是'desc'， 即最近的发送的前
     * @return array|static[]
     */
    public static function getUserSentMessages($userId, $orderType = 'desc')
    {
        return DB::table('messages')
            ->where('sender_id', $userId)
            ->leftJoin('messagesInboxs', 'messages.id', '=', 'messagesInboxs.message_id')
            ->leftJoin('users', 'messagesInboxs.receiver_id', '=', 'users.id')
            ->select(
                'messages.id AS id',
                'messages.title AS title',
                'messages.content AS content',
                'messages.created_at AS created_at',
                'messagesInboxs.read AS read',
                'messagesInboxs.receiver_id AS receiver_id',
                'users.username AS receiver_username',
                'users.email AS receiver_email',
                'users.head_image AS receiver_head_image'
            )
            ->orderBy('created_at', $orderType)
            ->get();
    }

    /**
     * 返回发送者的信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sender()
    {
        return $this->hasOne('User', 'sender_id', 'id');
    }

    /**
     * 返回接受者的信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function receiver()
    {
        return $this->belongsToMany('User', 'messagesInboxs', 'message_id', 'receiver_id');
    }

    protected $guarded = ['id'];

    protected $table = 'messages';
}
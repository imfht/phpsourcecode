<?php
namespace app\common\Model;

use think\Model;

class Follow extends Model
{

    protected $autoWriteTimestamp = true;
    /**关注
     * @param $uid
     * @return int|mixed
     */
    public function follow($uid)
    {
        $follow['who_follow'] = is_login();
        $follow['follow_who'] = $uid;
        if ($follow['who_follow'] == $follow['follow_who']) {
            //禁止关注和被关注都为同一个人的情况。
            return 0;
        }
        if ($this->where($follow)->count() > 0) {
            return 0;
        }
        $follow = $this->create($follow);

        clean_query_user_cache($uid, 'fans');
        clean_query_user_cache(is_login(), 'following');
        cache('atUsersJson_' . is_login(), null);
        /**
         * @param $to_uids 接收消息的用户们
         * @param string $title 消息标题
         * @param string $content 消息内容
         * @param string $url 消息指向的路径，U函数的第一个参数
         * @param array $url_args 消息链接的参数，U函数的第二个参数
         * @param int $from_uid 发送消息的用户
         * @param string $type 消息类型标识，对应各模块message_config.php中设置的消息类型
         * @param string $tpl 消息模板标识，对应各模块message_config.php中设置的消息模板
         */
        $user = query_user(array('id', 'nickname', 'space_url'));
        $this->cache($follow['who_follow'], $follow['follow_who'], null);
        model('Message')->sendMessage($uid, lang('_FANS_NUMBER_INCREASED_'), $user['nickname'] . lang('_CONCERN_YOU_WITH_PERIOD_'), 'ucenter/Index/index', array('uid' => is_login()),is_login(),'Ucenter');
        return $this->add($follow);
    }

    /**取消关注
     * @param $uid
     * @return mixed
     */
    public function unfollow($uid)
    {
        $follow['who_follow'] = is_login();
        $follow['follow_who'] = $uid;
        clean_query_user_cache($uid, 'fans');
        clean_query_user_cache(is_login(), 'following');
        cache('atUsersJson_' . is_login(), null);
        $user = query_user(array('id', 'nickname', 'space_url'));

        model('Message')->sendMessage($uid, lang('_NUMBER_OF_FANS_'), $user['nickname'] . lang('_CANCEL_YOUR_ATTENTION_WITH_PERIOD_'), 'ucenter/Index/index', array('uid' => is_login()),is_login(),'Ucenter');


        $this->cache($follow['who_follow'], $follow['follow_who'], null);
        return $this->where($follow)->delete();
    }

    public function isFollow($who_follow, $follow_who)
    {
        $follow = $this->cache($who_follow, $follow_who);
        if ($follow === false) {
            $follow = Db::name('Follow')->where(['who_follow' => $who_follow, 'follow_who' => $follow_who])->count();
            $follow++;
            $this->cache($who_follow, $follow_who, $follow);
        }
        return intval($follow) - 1;
    }

    public function getFollow($who_follow, $follow_who)
    {
        $follow = $this->where(['who_follow' => $who_follow, 'follow_who' => $follow_who])->find();
        return $follow;
    }

    public function S($who_follow, $follow_who, $data = '')
    {
        return cache('Core_follow_' . $who_follow . '_' . $follow_who, $data);
    }


    public function getFans($uid, $fields)
    {
        $map['follow_who'] = $uid;
        $fans = $this->where($map)->field('who_follow')->order('create_time desc')->paginate(10)->each(
            function($item,$key){
                $item->user = query_user($fields, $item->who_follow);
            }
        );

        return $fans;
    }

    public function getFollowing($uid, $fields)
    {
        $map['who_follow'] = $uid;
        $fans = $this->where($map)->field('follow_who')->order('create_time desc')->paginate(10)->each(
            function($item,$key){
                $item->user = query_user($fields, $item->who_follow);
            }
        );

        return $fans;
    }


    /**显示全部的好友
     * @param int $uid
     * @return mixed
     * @auth 陈一枭
     */
    public function getAllFriends($uid = 0)
    {
        if ($uid == 0) {
            $uid = is_login();
        }
        $model_follow = Db::name('Follow');
        $i_follow = $model_follow->where(['who_follow' => $uid])->limit(999)->select();
        foreach ($i_follow as $key => $user) {
            if ($model_follow->where(['follow_who' => $uid, 'who_follow' => $user['follow_who']])->count()) {
                continue;
            } else {
                unset($i_follow[$key]);
            }
        }
        return $i_follow;
    }


    public function getFollowList()
    {
        //获取我关注的人
        $result = $this->where(['who_follow' => get_uid()])->select();
        foreach ($result as &$e) {
            $e = $e['follow_who'];
        }
        unset($e);
        $followList = $result;
        $followList[] = is_login();
        return $followList;
    }


    /**关注
     * @param $who_follow
     * @param $follow_who
     * @return int|mixed
     */
    public function addFollow($who_follow, $follow_who, $invite = 0)
    {
        $follow['who_follow'] = $who_follow;
        $follow['follow_who'] = $follow_who;
        if ($follow['who_follow'] == $follow['follow_who']) {
            //禁止关注和被关注都为同一个人的情况。
            return 0;
        }
        if ($this->where($follow)->count() > 0) {
            return 0;
        }
        clean_query_user_cache($follow_who, 'fans');
        clean_query_user_cache($who_follow, 'following');
        cache('atUsersJson_' . $who_follow, null);

        $user = query_user(array('id', 'nickname', 'space_url'), $who_follow);
        if ($invite) {
            if ($who_follow < $follow_who) {
                $content = lang('_INVITED_') . $user['nickname'] . lang('_CONCERN_YOU_WITH_PERIOD_');
            } else {
                $content = lang('_YOURE_INVITING_THE_USER_') . $user['nickname'] . lang('_CONCERN_YOU_WITH_PERIOD_');
            }
        } else {
            if ($who_follow < $follow_who) {
                $content = lang('_SYSTEM_RECOMMENDED_USERS_') . $user['nickname'] . lang('_CONCERN_YOU_WITH_PERIOD_');
            } else {
                $content = lang('_NEW_USER_') . $user['nickname'] . lang('_CONCERN_YOU_WITH_PERIOD_');
            }
        }
        //发送消息
        model('Message')->sendMessage($follow_who, lang('_FANS_NUMBER_INCREASED_'), $content, 'ucenter/Index/index', array('uid' => $who_follow), $who_follow);

        return $this->save($follow);
    }

} 
<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2019/3/4
 * Time: 23:41
 */

namespace app\lib;

use app\chat\model\ChatCluster;
use app\chat\model\ChatGroup;
use app\chat\model\ChatMember;

class ChatRedis extends \Redis{
    protected static $user_list = 'user_list';
    protected static $chat_list = 'chat_list';
    protected static $friends_list = 'friends_list';
    protected static $cluster_list = 'cluster_list';
    protected static $fd_list = 'fd_list';

    public $member = null;
    public $group = null;
    public $cluster = null;
    /**
     * chatRedis constructor.
     * @param string $host
     * @param int $port
     */
    public function __construct($host='127.0.0.1',$port=6379){
        parent::connect($host,$port);
        $this->member = new ChatMember();
        $this->group = new ChatGroup();
        $this->cluster = new ChatCluster();
    }

    /**
     * 绑定用户
     * @param int $fd
     * @param string $user_id
     * @return $this
     */
    public function bindUser($fd,$user_id){
        $info = $this->member->getUserInfo($user_id);
        $info = $info->toArray();
        $info['fd']=$fd;
        $this
//            ->setOnline($fd,['user_id'=>$user_id,'online'=>'online'])
            ->set(self::$user_list.$fd,json_encode($info));
        return $this;
    }

    /**
     * 设置fd
     * @param $fd
     * @param $user_id
     * @return $this
     */
    public function bindFd($fd,$user_id){
        $this->set(self::$fd_list.$user_id,$fd);
        return $this;
    }

    /**
     * 获取fd
     * @param $user_id
     * @return bool|string
     */
    public function getFdByUserId($user_id){
        $result = $this->get(self::$fd_list.$user_id);
        return $result ? $result : '';
    }

    /**
     * 设置用户状态
     * @param $fd
     * @param $data
     * @param bool $isBind
     * @return $this
     */
    public function setOnline($fd,$data,$isBind=true){
        $this->member->setUserOnline($data['user_id'],$data['online']);
        if($isBind){
            $this->bindUser($fd,$data['user_id']);
        }
        return $this;
    }

    /**
     * 修改用户签名
     * @param $fd
     * @param $data
     * @return ChatRedis
     */
    public function setSign($fd,$data){
        $this->member->setSign($data['user_id'],$data['sign']);
        return $this->bindUser($fd,$data['user_id']);
    }


    /**
     * 获取用户id
     * @param int $fd
     * @return mixed
     */
    public function getUserId($fd=0){
        $result = json_decode($this->get(self::$user_list.$fd),true);
        return $result['user_id'];
    }

    /**
     * 获取用户名
     * @param int $fd
     * @return mixed
     */
    public function getUserNickname($fd=0){
        $result = json_decode($this->get(self::$user_list.$fd),true);
        return $result['username'];
    }

    /**
     * 获取用户信息
     * @param int $fd
     * @return mixed
     */
    public function getUser($fd=0){
        $result = json_decode($this->get(self::$user_list.$fd),true);
        return $result;
    }

    /**
     * 设置朋友列表
     * @param $fd
     * @param $user_id
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setFriends($fd,$user_id){
        $list = $this->group->getFriendsByMemberId($user_id);
        $this->set(self::$friends_list.$fd,json_encode($list));
        return $this;
    }

    /**
     * 获取朋友
     * @param int $fd
     * @return mixed
     */
    public function getFriends($fd){
        $result = json_decode($this->get(self::$friends_list.$fd),true);
        return $result;
    }

    /**
     * @param $fd
     * @param $user_id
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setGroups($fd,$user_id){
        $list = $this->cluster->getGroupsByMemberId($user_id);
        $this->set(self::$friends_list.$fd,json_encode($list));
        return $this;
    }

    /**
     *
     * @param $fd
     * @return mixed
     */
    public function getGroups($fd){
        $result = json_decode($this->get(self::$friends_list.$fd),true);
        return $result;
    }

    /**
     * 设置用户内容
     * @param int $fd
     * @param array $data
     * @return array
     */
    public function setUserChat($fd=0,$data=[]){
        $time = time();
        $_data = [
            'username'=>$data['mine']['username'],
            'avatar'=>$data['mine']['avatar'],
            'id'=> intval($data['mine']['id']),
            'type'=>$data['to']['type'],
            'content'=>$data['mine']['content'],
            'mine'=> false,
            'fromid'=>$data['mine']['id'],
            'timestamp'=> $time * 1000,
            'create_time'=> $time,
            'from'=> intval($data['mine']['id']),
            'to'=> intval($data['to']['id']),
        ];
        $_data1 = [
            'from'=> intval($data['mine']['id']),
            'to'=> intval($data['to']['id']),
            'create_time'=> $time,
            'type'=>$data['to']['type'],
            'content'=>$data['mine']['content'],
            'status'=> $this->getFdByUserId($data['to']['id']) ? 2 : 1
        ];
        $_data1 = json_encode($_data1);
        $this->rPush("chat_list.{$fd}",$_data1);
        return $_data;
    }

    /**
     * 获取聊天信息
     * @param int $fd
     * @param null $out
     * @return $this
     */
    public function getUserChat($fd=0,&$out=null){
        $data = $this->lRange("chat_list.{$fd}",0,-1);
        foreach ($data as $k => &$v){
            $v =json_decode($v,true);
        }
        $out = $data;
        return $this;
    }

    /**
     * 清除信息
     * @param int $fd
     */
    public function clear($fd=0){
        $this->delete(self::$user_list.$fd);
        $this->delete("chat_list.{$fd}");
        $this->delete(self::$friends_list.$fd);
        $this->delete(self::$fd_list.$fd);
        $this->delete(self::$cluster_list.$fd);
    }

    /**
     * 清除所有
     */
    public function clearAll(){
        $this->delete($this->keys(self::$user_list.'*'));
        $this->delete($this->keys("chat_list.*"));
        $this->delete($this->keys(self::$cluster_list.'*'));
        $this->delete($this->keys(self::$fd_list.'*'));
        $this->delete($this->keys(self::$friends_list.'*'));
    }
}
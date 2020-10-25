<?php
namespace app\chat\model;

use app\common\model\Qiniu;
use think\Model;

class ChatMember extends Model{

    public function getUserInfo($id=0,$field=''){
        $field = $field ? $field : 'id,account,username,avatar,sign,online';
        $result = $this->field($field)->find($id);
        $result['status'] = $this->getOnlineStatusAttr($result['online']);
        return $result;
    }

    /**
     * 更新
     * @param $id
     * @param $online
     */
    public function setUserOnline($id,$online){
        $info = $this->find($id);
        $info->online = $this->setOnlineStatusAttr($online);
        $info->last_time = time();
        $info->save();
    }

    /**
     * 修改签名
     * @param $id
     * @param $sign
     */
    public function setSign($id,$sign){
        $info = $this->find($id);
        $info->sign = $sign;
        $info->save();
    }

    /**
     * @param int $id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserFriends($id=0){
        $friends = new ChatFriends();
        $list = $friends->getFriendsByGroupId(1);
        return $list;
    }

    /**
     * 获取头像
     * @param $value
     * @return string
     */
    protected function getAvatarAttr($value){
        if(empty($value)){
            return '';
        }else{
            return (new Qiniu())->getPicPath($value);
        }
    }

    /**
     * 获取在线状态
     * @param $value
     * @return mixed|string
     */
    protected function getOnlineStatusAttr($value){
        $online = [
            '1'=>'online',
            '2'=>'hide',
            '3'=>'offline',
        ];
        return $online[$value];
    }

    /**
     * 设置在线状态
     * @param $value
     * @return mixed
     */
    public function setOnlineStatusAttr($value){
        $online = [
            'online'=> 1,
            'hide'=> 2,
            'offline'=> 3,
        ];
        return $online[$value];
    }
}
<?php
namespace app\common\model;

use think\Model;

class ChatMember extends Model{

    public function getUserInfo($id=0,$field=''){
        $field = $field ? $field : 'id,account,username,avatar,sign,online';
        $result = $this->field($field)->find($id);
        $result['status'] = $this->getOnlineStatusAttr($result['online']);
        return $result;
    }

    /**
     * @param int $id
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
}
<?php
namespace app\common\model;

use think\Model;

class ChatFriends extends Model{

    /**
     * @param int $group_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFriendsByGroupId($group_id=1){
        $list = $this
            ->with('friends')
            ->where('group_id','eq',$group_id)
            ->select();
        return $list;
    }

    public function friends(){
        return $this->hasMany('ChatMember','id','member_id')
            ->fieldRaw('id,account,username,avatar,sign');
    }

    /**
     * 添加用户组
     * @param $member_id
     * @param $groupname
     * @param $avatar
     * @param $sign
     * @param $type
     * @param int $out
     * @return bool
     */
    public function addNew($member_id,$groupname,$avatar,$sign,$type,&$out=0){
        $data = [
            'member_id'=>$member_id,
            'groupname'=>$groupname,
            'avatar'=>$avatar,
            'sign'=>$sign,
            'type'=>$type,
            'create_time'=>time()
        ];
        if(!$this->insert($data)){
            $out = '上传失败';
            return false;
        }
        $out = $this->getLastInsID();
        return true;
    }
}
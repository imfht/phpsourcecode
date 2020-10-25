<?php
namespace app\chat\model;

use app\common\model\Qiniu;
use think\Model;

class ChatGroup extends Model{

    /**
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
            'create_time'=>time()
        ];
        if(!$id = $this->insertGetId($data)){
            $out = '上传失败';
            return false;
        }
        $out = $id;
        return true;
    }

    /**
     * 获取用户组
     * @param int $id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserGroupByUid($id=0){
        $list = $this
            ->where('member_id','eq',$id)
            ->distinct('groupname')
            ->select();
        return $list;
    }

    /**
     * @param int $id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFriendsById($id){
        $list = $this
            ->with(['friends'])
            ->field('id,groupname,avatar,sign')
            ->where('id','eq',$id)
            ->select();
        $member = new ChatMember();
        foreach ($list as $k => &$v){
            $data = [];
            foreach ($v['friends'] as $k1 => &$v1){
                $_member = $member->getUserInfo($v1['member_id']);
                $v1['account'] = $_member['account'];
                $v1['username'] = $_member['username'];
                $v1['avatar'] = $_member['avatar'];
                $v1['sign'] = $_member['sign'];
                $v1['status'] = $_member['status'];
                array_push($data,$v1);
            }
            unset($v['friends']);
            $v['list']= $data;
        }
        return $list;
    }

    /**
     * @param int $member_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFriendsByMemberId($member_id){
        $list = $this
            ->with(['friends'])
            ->field('id,groupname,avatar,sign')
            ->where('member_id','eq',$member_id)
            ->select();
        $member = new ChatMember();
        foreach ($list as $k => &$v){
            $data = [];
            foreach ($v['friends'] as $k1 => &$v1){
                $_member = $member->getUserInfo($v1['id']);
                $v1['account'] = $_member['account'];
                $v1['username'] = $_member['username'];
                $v1['avatar'] = $_member['avatar'];
                $v1['sign'] = $_member['sign'];
                $v1['status'] = $_member['status'];
                array_push($data,$v1);
            }
            unset($v['friends']);
            $v['list']= $data;
        }
        return $list;
    }

    public function friends(){
        return $this
            ->hasMany('ChatFriends','group_id')
            ->field('member_id as id,group_id');
    }

    protected function getAvatarAttr($value){
        return $value?(new Qiniu())->getPicPath($value):'';
    }
}
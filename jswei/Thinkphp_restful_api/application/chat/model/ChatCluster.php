<?php
namespace app\chat\model;

use app\common\model\Qiniu;
use think\Model;

class ChatCluster extends Model{
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
            'owner'=>$member_id,
            'groupname'=>$groupname,
            'avatar'=>$avatar,
            'sign'=>$sign,
            'create_time'=>time()
        ];
        if(!$this->insert($data)){
            $out = '上传失败';
            return false;
        }
        $out = $this->getLastInsID();
        return true;
    }

    /**
     * @param $member_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGroupsFriendsByMemberId($member_id){
        $list = $this
            ->field('id,groupname,avatar,sign,owner,members')
            ->where('member_id','eq',$member_id)
            ->select();
        $member = new ChatMember();
        foreach ($list as $k => $v){
            $_member = $member::field('id,account,username,avatar,sign')
                ->get($v['owner']);
            $_member_list = $member::field('id,account,username,avatar,sign')
                ->whereIn('id',$v['members'])
                ->select();
            $list[$k]['owner'] = $_member;
            $list[$k]['members'] = count($_member_list);
            $list[$k]['list'] = $_member_list;
        }
        return $list;
    }

    /**
     * 根据组id获取用户
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGroupsFriendsById($id){
        $list = $this
            ->field('id,groupname,avatar,sign,owner,members')
            ->find($id);
        $member = new ChatMember();
        $_member_list = $member::field('id,account,username,avatar,sign,online')
            ->whereIn('id',$list['members'])
            ->select();
        foreach ($_member_list as $k=>$v){
            $_member_list[$k]['status'] = $v['online']==1?'online':'offline';
            unset($_member_list[$k]['online'] );
        }
        $list['list'] = $_member_list;
        $list['members'] = count($_member_list);
        return $list;
    }

    /**
     * 获取用户群组信息
     * @param $member_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGroupsByMemberId($member_id){
        $list = $this
            ->field('id,groupname,avatar,sign,owner,members')
            ->where('owner','eq',$member_id)
            ->select();
        return $list;
    }

    public function member(){
        return $this
            ->hasOne('ChatMember','id','owner');
    }

    public function getAvatarAttr($value){
        return $value ? (new Qiniu())->getPicPath($value):'';
    }
}
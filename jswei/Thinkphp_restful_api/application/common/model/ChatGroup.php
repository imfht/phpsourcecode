<?php
namespace app\common\model;

use think\Model;

class ChatGroup extends Model{
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
    public function addNew($member_id,$groupname,$avatar,$sign,$type,&$out=0){
        $data = [
            'member_id'=>$member_id,
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
                $_member = $member::field('id,account,username,avatar,sign,online')
                   ->get($v1['member_id']);
                $v1['account'] = $_member['account'];
                $v1['username'] = $_member['username'];
                $v1['avatar'] = $_member['avatar'];
                $v1['sign'] = $_member['sign'];
                array_push($data,$v1);
            }
            unset($v['friends']);
            $v['list']= $data;
        }
        return $list;
    }

    public function getGroupsByMemberId($member_id){
        $list = $this
            ->with(['friends'])
            ->field('id,groupname,avatar,sign')
            ->where('member_id','eq',$member_id)
            ->select();

        /*$member = new ChatMember();
        foreach ($list as $k => &$v){
            $data = [];
            foreach ($v['friends'] as $k1 => &$v1){
                $_member = $member::field('id,account,username,avatar,sign')
                    ->get($v1['member_id']);
                $v1['account'] = $_member['account'];
                $v1['username'] = $_member['username'];
                $v1['avatar'] = $_member['avatar'];
                $v1['sign'] = $_member['sign'];
                $v1['status'] = $v1['online']==1?'online':'offline';
                unset($v1['online']);
                array_push($data,$v1);
            }
            unset($v['friends']);
            $v['list']= $data;
        }
        return $list;*/
    }

    public function friends(){
        return $this
            ->hasMany('ChatFriends','group_id')
            ->field('id,member_id,online,group_id');
    }

    public function member(){
        return $this->hasOne('ChatMember','id','member_id')
            ->bind([
                'account',
                'nickname',
                'avatar',
                'sign'
            ]);
    }

    public function getAvatarAttr($value){
        $qiniu = new Qiniu();
        return $value?$qiniu->getPicPath($value):'';
    }
}
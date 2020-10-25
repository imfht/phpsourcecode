<?php
namespace app\ucenter\model;

use think\Model;

class InviteUserInfo extends Model
{

    /**
     * 添加兑换邀请名额记录
     * @param int $type_id
     * @param int $num
     * @return bool|mixed
     */
    public function addNum($type_id=0,$num=0)
    {
        $map['uid']=is_login();
        $map['invite_type']=$type_id;
        if($this->where($map)->count()){
            $res=$this->where($map)->setInc('num',$num);
        }else{
            $data['uid']=is_login();
            $data['invite_type']=$type_id;
            $data['num']=$num;
            $data['already_num']=0;
            $data['success_num']=0;
            $res=$this->save($data);
        }
        return $res;
    }

    /**
     * 降低可邀请名额，增加已邀请名额
     * @param int $type_id
     * @param int $num
     * @return bool
     */
    public function decNum($type_id=0,$num=0){
        $map['uid']=is_login();
        $map['invite_type']=$type_id;
        $res=$this->where($map)->setDec('num',$num);//减少可邀请数目
        $this->where($map)->setInc('already_num',$num);//增加已邀请数目
        return $res;
    }

    /**
     * 保存数据
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function saveData($data=array(),$id=0)
    {
        $result=$this->where(['id'=>$id])->save($data);
        return $result;
    }

    /**
     * 邀请成功后数据变更
     * @param int $type_id
     * @param int $uid
     * @return bool
     */
    public function addSuccessNum($type_id=0,$uid=0){
        $map['uid']=$uid;
        $map['invite_type']=$type_id;
        $res=$this->where($map)->setInc('success_num');//增加邀请成功数目
        return $res;
    }

    /**
     * 获取用户邀请信息
     * @param string $map
     * @return mixed
     */
    public function getInfo($map='')
    {
        $data=$this->where($map)->find();
        return $data;
    }

    /**
     * 初始化查询数据
     * @param array $list
     * @return array
     */
    private function _initSelectData($list=[])
    {
        $inviteTypeModel=model('ucenter/InviteType');

        foreach($list as &$val){
            $inviteType=$inviteTypeModel->getSimpleData(array('id'=>$val['invite_type']));
            $val['invite_type_title']=$inviteType['title'];
            $val['user']=query_user('nickname',$val['uid']);
            $val['user']='['.$val['uid'].']'.$val['user'];
        }
        unset($val);

        return $list;
    }
}
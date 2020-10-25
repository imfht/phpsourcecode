<?php
namespace app\ucenter\model;

use think\Db;
use think\Model;

class InviteLog extends Model
{

    /**
     * 添加邀请注册成功日志
     * @param array $data
     * @param int $role
     * @return mixed
     */
    public function addData($data=array(),$role=0)
    {
        $inviter_user=query_user('nickname',$data['inviter_id']);
        $user=query_user('nickname',$data['uid']);
        $role=Db::name('Role')->where(array('id'=>$role))->find();
        $data['content']="{$user} 接受了 {$inviter_user} 的邀请，注册了 {$role['title']} 身份。";
        $data['create_time']=time();

        $result=$this->add($data);
        return $result;
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
            $val['inviter']=query_user('nickname',$val['inviter_id']);
            $val['inviter']='['.$val['inviter_id'].']'.$val['inviter'];
        }
        unset($val);
        return $list;
    }
} 
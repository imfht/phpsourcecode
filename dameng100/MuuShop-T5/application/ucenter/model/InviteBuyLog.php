<?php
namespace app\ucenter\model;

use think\Model;

class InviteBuyLog extends Model
{
    /**
     * 添加用户兑换名额记录
     * @param int $type_id
     * @param int $num
     * @return mixed
     */
    public function buy($type_id = 0, $num = 0)
    {
        $invite_type=Db::name('InviteType')->where(['id'=>$type_id])->find();
        $user=query_user('nickname');
        $data['content']="{$user} 在 ".time_format(time()).lang('_TIME_TO_BUY_').$num.' 个 '.$invite_type['title'].lang('_INVITATION_');

        $data['uid']=is_login();
        $data['invite_type']=$type_id;
        $data['num']=$num;
        $data['create_time']=time();

        $result=$this->save($data);
        return $result;
    }

    /**
     * 初始化查询出的数据
     * @param array $list
     * @return array
     */
    private function _initSelectData($list=array())
    {
        $inviteTypeModel=model('ucenter/InviteType');
        foreach($list as &$val){
            $inviteType=$inviteTypeModel->getSimpleData(['id'=>$val['invite_type']]);
            $val['invite_type_title']=$inviteType['title']?$inviteType['title']:'[已删除类型]';
            $val['user']=query_user('nickname',$val['uid']);
            $val['user']='['.$val['uid'].']'.$val['user'];
        }
        unset($val);
        return $list;
    }
} 
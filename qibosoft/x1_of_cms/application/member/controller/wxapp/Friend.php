<?php
namespace app\member\controller\wxapp;

use app\common\controller\MemberBase;
use app\common\model\Friend AS FriendModel;

class Friend extends MemberBase
{
    /**
     * 检查当前用户是否是某人的粉丝
     * @param number $uid 博主UID
     */
    public function ckgz($uid=0){
        $where = [
            'suid'=>$uid,
            'uid'=>$this->user['uid'],
        ];
        $rs = FriendModel::get($where);
        if($rs){
            return $this->ok_js($rs);
        }else{
            return $this->err_js('还没关注');
        }
    }
    
    public function act($uid=0,$type=''){
        if ($type=='add') {
            return $this->add($uid);
        }elseif ($type=='del') {
            return $this->del($uid);
        }elseif ($type=='bad') {
            return $this->bad($uid);
        }
    }
    
    /**
     * 加对方为好友,或关注对方
     * @param number $uid 对方UID
     */
    public function add($uid=0){        
        if ($uid==$this->user['uid']) {
            return $this->err_js('你不能关注自己,加自己为好友!');
        }
        $type = 1;  //默认是单向好友
        $heinfo = getArray(FriendModel::where('suid',$this->user['uid'])->where('uid',$uid)->find()); //先看看自己在对方那里的状态
        $myinfo = getArray(FriendModel::where('suid',$uid)->where('uid',$this->user['uid'])->find());
        
        if ($myinfo) {  //我已关注过他
            if($myinfo['type']==-1){  //之前是我的黑名单
                $type = 2;
                if ($heinfo['type']==-1) {
                    $msg = '你把Ta取消了黑名单,但你还在Ta的黑名单中。';
                }elseif($heinfo){                    
                    FriendModel::where('id',$heinfo['id'])->update(['type'=>$type]);
                    $msg = '你把Ta取消了黑名单,你们是双向好友了';
                }else{
                    $msg = '你把Ta取消了黑名单,但Ta还没关注你';
                }
                FriendModel::where('id',$myinfo['id'])->update(['type'=>$type]);
                return $this->ok_js(['type'=>$type],$msg);
            }elseif($myinfo['type']==1){ //之前是我的单向好友
                $type = 2;
                if ($heinfo['type']==-1) {
                    $msg = '你把Ta加了好友,但你还在Ta的黑名单中。';
                }elseif($heinfo){                    
                    FriendModel::where('id',$heinfo['id'])->update(['type'=>$type]);
                    $msg = '你们是双向好友了';
                }else{
                    $msg = '你把Ta加了好友,但Ta还没关注你';
                }
                FriendModel::where('id',$myinfo['id'])->update(['type'=>$type]);
                return $this->ok_js(['type'=>$type],$msg);
            }else{
                return $this->err_js('请不要重复操作!');
            }            
        }else{  //我还没关注过他
            if($heinfo){
                if($heinfo['type']==-1){
                    $msg = '对方已把你加入了黑名单,虽然你把TA当好友,但TA不把你当好友';
                }else{
                    $msg = '你们已互相关注,成为了双向好友!';
                    $type = 2;
                    FriendModel::where('id',$heinfo['id'])->update(['type'=>$type]);
                }
            }else{
                $msg = '成功关注Ta,不过Ta还没关注你!';
            }
            $result = FriendModel::create([
                'suid'=>$uid,
                'uid'=>$this->user['uid'],
                'type'=>$type,
            ]);
            if ($result) {
                return $this->ok_js(['type'=>$type],$msg);
            }else{
                return $this->err_js('数据库操作失败!');
            }
        }
        
        /*
         * 
        $info = $heinfo;$rs = $myinfo;
        if ($info) {
            if($rs){
                if($rs['type']==-1){
                    $msg = '对方把你加入了黑名单,虽然你把他当好友,但他不把你当好友';
                }else{
                    $msg = '你们成为了双向好友!';
                    $type = 2;
                    FriendModel::where('id',$rs['id'])->update(['type'=>$type]);
                }
            }elseif($info['type']==-1){ //从黑名单重新加好友
                $type = 2;
                $msg = '重新加好友成功，对方未必是你的好友';
            }else{
                $type = 2;
                $msg = '你们成为了双向好友。';
                FriendModel::create([
                    'suid'=>$uid,
                    'uid'=>$this->user['uid'],
                    'type'=>$type,
                ]);
            }            
            FriendModel::where('id',$info['id'])->update(['type'=>$type]);
        }elseif($rs){
            $msg = '加为单向好友成功!';
            FriendModel::where('id',$rs['id'])->update([
                'suid'=>$this->user['uid'],
                'uid'=>$uid,
                'type'=>$type,                
            ]);
        }else{
            return $this->err_js('资料有误');
        }
        return $this->ok_js(['type'=>$type],$msg);
        */
    }
    
    /**
     * 移除对方
     * @param number $uid
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function del($uid=0){
        $info = FriendModel::where('suid',$this->user['uid'])->where('uid',$uid)->find();   //获取对方信息
        if ($info && $info['type']!=-1) {   //自己不在对方黑名单
            FriendModel::where('id',$info['id'])->update(['type'=>1]);
        }
        $result = FriendModel::where('suid',$uid)->where('uid',$this->user['uid'])->delete();
        if ($result) {
            return $this->ok_js();
        }else{
            return $this->err_js('移除失败');
        }
        /*
        $info = FriendModel::where('suid',$uid)->where('uid',$this->user['uid'])->find();   //获取对方信息
        if ($info && $info['type']!=-1) {   //自己不在对方黑名单
            if($info['type']==2){   //如果是双向好友的话,就要降为单向好友
                FriendModel::where('id',$info['id'])->update(['type'=>1]);
            }            
        }
        $result = FriendModel::where('suid',$this->user['uid'])->where('uid',$uid)->delete();
        if ($result) {
            return $this->ok_js();
        }
        $result = FriendModel::where('suid',$uid)->where('uid',$this->user['uid'])->delete();
        if ($result) {
            return $this->ok_js();
        }else{
            return $this->err_js('删除失败');
        }
        */
    }
    
    /**
     * 把对方加黑名单
     * @param number $uid
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function bad($uid=0){
        $heinfo = getArray(FriendModel::where('suid',$this->user['uid'])->where('uid',$uid)->find());
        if($heinfo['type']==2){   //如果是双向好友的话,就要降为单向好友
            FriendModel::where('id',$heinfo['id'])->update(['type'=>1]);
        }
        $myinfo = getArray(FriendModel::where('suid',$uid)->where('uid',$this->user['uid'])->find());
        if ($myinfo) {
            if ($myinfo['type']==-1) {
                return $this->err_js('已经是黑名单了');
            }
            $result = FriendModel::where('id',$myinfo['id'])->update(['type'=>-1]);
            if ($result) {
                return $this->ok_js();
            }else{
                return $this->err_js('更新失败');
            }            
        }else{
            $result = FriendModel::create([
                'suid'=>$uid,
                'uid'=>$this->user['uid'],
                'type'=>-1,
            ]);
            if ($result) {
                return $this->ok_js();
            }else{
                return $this->err_js('数据库操作失败!');
            }
        }
    }
}

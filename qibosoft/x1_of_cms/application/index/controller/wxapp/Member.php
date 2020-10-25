<?php
namespace app\index\controller\wxapp;

use app\common\controller\IndexBase;
use app\common\model\User;

//小程序 用户相关
class Member extends IndexBase
{
    /**
     * 按距离列出用户
     * @param string $point
     * @param number $rows
     * @return \think\Paginator
     */
    public function get_near($point='113.224932,23.184547',$rows=10){
        $listdb = User::getListByMap([],$point,$rows);
        $array = getArray($listdb);
        foreach($array['data'] AS $key => $rs){
            $this->format_field($rs);
            $rs['picurl'] = $rs['icon'];
            $rs['id'] = $rs['uid'];
            $rs['title'] = $rs['username'];
            $rs['url'] = get_url('user',$rs['uid']);
            $array['data'][$key] = $rs;
        }
        return $this->ok_js($array); 
    }
    
    /**
     * 统计全站用户总数
     * @return void|unknown|\think\response\Json
     */
    public function get_total(){
        $num = User::where([])->count('id');
        return $this->ok_js(['num'=>$num]);
    }
    
    private function format_field(&$rs=[]){
        $rs = \app\common\fun\Member::format($rs,$this->user['uid']);
        return $rs;
    }
    
    /**
     * 获取用户列表
     * @param string $type
     * @param number $rows
     * @return void|unknown|\think\response\Json
     */
    public function get_list($type='',$rows=1,$name=''){
        $map = [];
        if ($name!='') {
            $map['username'] = ['like','%'.$name.'%'];
        }
        $data_list = User::where($map)->order("uid desc")->paginate($rows);
        $data_list->each(function($rs,$key){            
            $this->format_field($rs);
            return $rs;
        });
//         $listdata = User::where([])->limit($rows)->column(true);
//         $listdata = array_values($listdata);
         return $this->ok_js($data_list);
    }
    
    /**
     * 根据UID获取用户资料
     * @param number $uid
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function getbyid($uid=0){
        if(empty($uid)){
            return $this->err_js('UID不存在');
        }
        $user = get_user($uid,'uid');
        if ($user) {
            $this->format_field($user);
            return $this->ok_js($user);
        }else{
            return $this->err_js('用户不存在');
        }
    }
    
    /**
     * 根据一串UID获取多个用户资料
     * @param string $uid
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function getbyids($uids='',$rows=500){
        $detail = explode(',', $uids);
        $uid_array = [];
        foreach($detail AS $uid){
            if (empty($uid)) {
                continue;
            }
            $uid_array[] = intval($uid);
        }
        if(empty($uid_array)){
            return $this->err_js('uids不存在');
        }
        $listdb = User::where('uid','in',$uid_array)->paginate($rows);
        $listdb = getArray($listdb);
        foreach($listdb['data'] AS $key=>$rs){
            $listdb['data'][$key] = $this->format_field($rs);
        }
        if ($listdb) {
            return $this->ok_js($listdb);
        }else{
            return $this->err_js('数据不存在');
        }
    }
    
    /**
     * 根据用户名获取用户的UID
     * @param string $name
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function get_uid($name=''){
        $user = get_user($name,'username');
        if ($user) {
            return $this->ok_js(['uid'=>$user['uid']]);
        }else{
            return $this->err_js('用户不存在');
        }
    }
    
    
}

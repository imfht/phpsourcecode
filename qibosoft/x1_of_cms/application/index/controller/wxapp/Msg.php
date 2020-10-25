<?php
namespace app\index\controller\wxapp;

use app\common\model\Msg AS Model;
use app\common\model\Msguser AS MsguserModel;
use app\common\controller\IndexBase;


class Msg extends IndexBase{
    
    public function index(){
        return $this->ok_js();
    }
    

    /**
     * 更新用户列表及消息是否已读
     * @param number $uid 正数其它用户UID,负数,圈子ID
     * @param number $id 消息ID
     */
    public function update_user($uid=0,$id=0){
        if (empty($this->user)) {
            return $this->err_js('还没登录');
        }elseif($uid==$this->user['uid']){
            return $this->err_js('不能自己给自己更新');
        }elseif(empty($uid)){
            return $this->err_js('UID不存在');
        }
        MsguserModel::add($this->user['uid'],$uid);
        if($uid>0){ //私聊当中的状态
            $map = [
                'id'=>$id,
                'ifread'=>0,
                'touid'=>$this->user['uid'],
            ];
            Model::where($map)->update([                
                'ifread'=>1,
            ]);
        }
        return $this->ok_js();
    }
    
    /**
     * 设置当前用户的群发消息组
     * @param number $uid 负数是圈子ID,正数是私聊时对方的UID
     * @param string $client_id WS生成的客户ID
     */
    public function bind_group($uid=0,$client_id=''){
        if (empty($uid)) {
            return $this->err_js('用户UID或者圈子ID不存在');
        }elseif (empty($client_id)) {
            return $this->err_js('客户ID不存在');
        }
        fun("Gatewayclient@user_join_group",$this->user['uid'],$uid,$client_id);
        return $this->ok_js();
    }
    
    /**
     * 获取某个人跟它人或者是圈子的会话记录
     * @param number $uid 正数用户UID,负数圈子ID
     * @param number $id 某条消息的ID
     * @param number $rows 取几条
     * @param number $maxid 消息中最新的那条记录ID
     * @param number $is_live 是否在视频直播
     * @param number $msg_id 归属主题 ID
     * @param number $msg_sys 归属主题的频道 ID
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function get_more($uid=0,$id=0,$rows=5,$maxid=0,$is_live=0,$msg_id=0,$msg_sys=0,$my_uid=0){
        if ($my_uid) {
            if ( (empty($this->user) && $my_uid<9999999) || ($this->user&&$this->user['uid']!=$my_uid) ) {
                return $this->err_js("my_uid数值有误");
            }
        }else{
            $my_uid = $this->user['uid'];
        }
        if (($uid>=0 || $id>0) && empty($this->user) && empty($my_uid)) {
            return $this->err_js("请先登录");
        }
        $qun_user = $qun_info = '';
        if ($uid<0) {
            if(!modules_config('qun')){
                return $this->err_js('你没有安装圈子模块!');
            }
            if($maxid<1){   //首次加载
                $qun_info = \app\qun\model\Content::getInfoByid(abs($uid),true);
                if ($this->user) {
                    $qun_user = \app\qun\model\Member::where([
                        'aid'=>abs($uid),
                        'uid'=>$this->user['uid'],
                    ])->find();
                    $qun_user = $qun_user?getArray($qun_user):[];
                }
            }
            isset($qun_info['_viewlimit']) || $qun_info['_viewlimit'] = $qun_info['viewlimit'];
            if($qun_info['_viewlimit'] && empty($this->admin) && $qun_info['uid']!=$this->user['uid']){
                if (empty($qun_user)) {
                    return $this->err_js('你不是本圈子成员,无权查看聊天内容!');
                }elseif ($qun_user['type']==0){
                    return $this->err_js('你还没通过审核,无权查看聊天内容!');
                }
            }
        }
        
        $array = model::list_moremsg($my_uid,$uid,$id,$rows,$maxid,$msg_id,$msg_sys);
        $array['qun_info'] = $qun_info;
        $array['qun_userinfo'] = $qun_user;
        if ($this->user) {
            $array['userinfo'] = [
                'uid'=>$this->user['uid'],
                'username'=>$this->user['username'],
                'nickname'=>$this->user['nickname'],
                'icon'=>tempdir($this->user['icon']),
                'groupid'=>$this->user['groupid'],
            ];
        }else{
            $array['userinfo'] = ['uid'=>$my_uid?:0,'username'=>'','groupid'=>0,'icon'=>''];
        }
        
        if ($maxid<1) { //首次加载
            $array['ws_url'] = fun('Gatewayclient@client_url'); //APP要用到
            if ($this->user) {
                //更新最后的访问时间,也即把历史消息标为已读
                MsguserModel::where('uid',$this->user['uid'])->where('aid',$uid)->update(['list'=>time()]);
                if($uid>=0){ //干脆点,把第二页第三页未读的也一起标注为已读了
                    model::where('touid',$this->user['uid'])->where('uid',$uid)->where('ifread',0)->update(['ifread'=>1]);
                }
            }            
        }
        
        if ($is_live) {
//             $live_array = cache('live_qun');
//             $data = $this->request->post();
//             if($live_array['qun'.$uid]['flv_url']!=$data['flv_url']){
//                 fun('alilive@add',$this->user['uid'],$uid,'qun',$data);
//             }
//             $live_array['qun'.$uid] = [
//                 'flv_url'=>$data['flv_url'],
//                 'm3u8_url'=>$data['m3u8_url'],
//                 'rtmp_url'=>$data['rtmp_url'],
//                 'time'=>time(),
//             ];
//             cache('live_qun',$live_array);
        }elseif($uid<1){    //代表群聊
//             $live_array = cache('live_qun');    //这里有个BUG,如果进后台操作过东西,缓存就会被清空,导致这里没数据
//             if($live_array['qun'.$uid]){
//                 $live_array['qun'.$uid]['time'] = 0;    //此参数将弃用
//                 $live_array['qun'.$uid]['push_url']='';
//                 $array['live_video'] = $live_array['qun'.$uid];
//             }elseif ($live_array['vod_voice'.$uid]){
//                 $array['vod_voice'] = $live_array['vod_voice'.$uid];
//             }
            $array['live'] = fun('Qun@live',abs($uid))?:''; //圈子的活跃信息,比如直播之类的
            if($array['live']){unset($array['live']['service_video']);}
            //unset($array['live']['service_video']['push_url']);
        }
        $array['chatmod'] = $this->get_chat_mod($uid,$uid<0?get_user($qun_info['uid'])['groupid']:$this->user['groupid']);  //群聊模块
        
        return $this->ok_js($array);
    }
    
    /**
     * 获取群聊模块
     * @param number $uid 用户是正数,圈子是负数
     * @param number $groupid uid是负数则是圈主用户组,否则就是当前用户的用户组
     * @return array
     */
    public function get_chat_mod($uid=0,$groupid=0){
        if(input('inapp')){
            $pcwap = 3;
        }elseif(in_wap()){
            $pcwap = 1;
        }else{
            $pcwap = 2;
        }
        $array = fun('chatmod@get',$pcwap,$uid<1?1:(in_wap()?2:0),$uid<0?abs($uid):0,$groupid);  //群聊模块
        return $array;
    }
    
    /**
     * 调取各个圈子的最新留言
     * @return void|unknown|\think\response\Json
     */
    public function newmsg(){
        $data = [];
        $array = model::where('qun_id','>',0)->order('id desc')->limit(10)->column(true);
        foreach($array AS $rs){
            $rs['username'] = get_user_name($rs['uid']);
            $rs['icon'] = get_user_icon($rs['uid']);
            $rs['time'] = format_time($rs['create_time'],true);
            $rs['qun_name'] = fun('qun@getByid',$rs['qun_id'])['title'];
            $rs['user_url'] = get_url('user',$rs['uid']);
            $rs['qun_url'] = get_url('msg',-$rs['qun_id']);
            $rs['title'] = get_word(del_html($rs['content']), 50);            
            $data[] = $rs;
        }
        return $this->ok_js($data);
    }
}

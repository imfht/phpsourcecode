<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 站内短消息
 * @package app\admin\model
 */
class Msg extends Model
{
    //protected $table = '__MEMBERDATA__';
	
	//主键不是ID,要单独指定
	//public $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    protected static $map;

    /**
     * 发送消息
     * @param array $data 数据内容
     * @param unknown $admin 是否是管理员身份操作
     * @param string $push 是否推消息,比如即时聊天的时候要用到
     * @return string[]|unknown
     */
    public static function add($data=[],$admin=null,$push=false){
        $info = Friend::where('uid',$data['touid'])->where('suid',$data['uid'])->find();
        if ($info['type']==-1) {
            return ['errmsg'=>'对方把你列入了黑名单,因此无法给他发消息'];
        }
        if (empty($admin) && empty($info)) {
            if (cache('pm_msg_'.$data['uid'])) {
                return ['errmsg'=>'请不要那么频繁的发送消息'];
            }
            cache('pm_msg_'.$data['uid'],time(),3);
        }
        
        //钩子接口
        get_hook('add_msg_begin',$data,[],['admin'=>$admin,'push'=>$push],true);
        hook_listen('add_msg_begin',$data,['admin'=>$admin,'push'=>$push]);
        
        $result = parent::create($data);
        if($data['uid']>0 && $data['touid']>0){
            $myinfo = Friend::where('uid',$data['uid'])->where('suid',$data['touid'])->find();
            if (empty($myinfo)) {
                //给用户发消息,就相当于成为他的粉丝
                Friend::create([
                    'suid'=>$data['touid'],
                    'uid'=>$data['uid'],
                    'type'=>$info['type']==1 ? 2 : 1,
                ]);
            }
            if ($info){
                if ($info['type']==1) {
                    Friend::where('id',$info['id'])->update(['type'=>2]);   //把对方也设置为双向好友
                    if ($myinfo['type']!=-1) {
                        Friend::where('id',$myinfo['id'])->update(['type'=>2]);
                    }
                }else{
                    Friend::where('id',$info['id'])->update(['update_time'=>time()]);
                }
            }          
        }
        if ($result) {
            
            //钩子接口
            get_hook('add_msg_end',$data,[],['admin'=>$admin,'push'=>$push,'id'=>$result->id],true);
            hook_listen('add_msg_end',$data,['admin'=>$admin,'push'=>$push,'id'=>$result->id]);
            
            if ($push) {
                self::push_msg($result->id);
            }
            return $result->id;
        }        
    }
    
    /**
     * 推数据
     * @param number $id
     */
    public static function push_msg($id=0){
        $info = self::format_msg(getArray(self::get($id)));
        $msg_array = [
            'type'=>'newmsg',
            'data'=>[$info],
        ];
        $msg_array['ext']['maxid'] = $id;
        $qun_id = $info['qun_id'];
        if ($qun_id) {//代表群聊 查找是否有直播数据
            $live_array = cache('live_qun');    //这里有个BUG,如果进后台操作过东西,缓存就会被清空,导致这里没数据
            if($live_array['qun-'.$qun_id]){
                $live_array['qun-'.$qun_id]['time'] = 0;    //此参数将弃用
                $live_array['qun-'.$qun_id]['push_url']='';
                $msg_array['ext']['live_video'] = $live_array['qun-'.$qun_id];
            }
        }
        fun("Gatewayclient@send_to_group",$info['uid'],$qun_id?-$qun_id:$info['touid'],$msg_array);
    }
    
    /**
     * 查找往来消息
     * @param number $myuid 当前登录用户UID
     * @param number $uid 他人UID 负数代表圈子ID
     * @param number $id 内容详情ID
     * @param number $rows 取几条
     * @param number $maxid 指定大于某个ID的内容
     * @param number $msg_id 归属主题 ID
     * @param number $msg_sys 归属主题的频道 ID
     * @return array|NULL[]|unknown
     */
    public static function list_moremsg($myuid=0,$uid=0,$id=0,$rows=10,$maxid=0,$msg_id=0,$msg_sys=0){
        $rows<1 && $rows=10;
        if($uid>0){
            cache('msg_time_'.$myuid.'-'.$uid,time(),60);  //把自己的操作时间做个标志
            $from_time = cache('msg_time_'.$uid.'-'.$myuid); //查看对方给自己的最后操作时间
        }elseif($uid<0){    //负数代表圈子ID
            $from_time = 0;
            $c_array = cache('msg_time_'.$uid)?:[];
            unset($c_array[$myuid]);
            $from_time = end($c_array);
            $c_array[$myuid] = time();
            cache('msg_time_'.$uid,$c_array,10);
        }
        
        if($uid<0){
            self::$map['a'] = [];
            self::$map['b'] = [
                'qun_id'=>abs($uid),
            ];
            if($maxid>0){
                self::$map['b']['id'] = ['>',$maxid];
            }
        }else{
            self::$map['a'] = [
                'touid'=>$myuid,
                'uid'=>$uid,
                'id'=>['<=',$id],
            ];
            
            self::$map['b'] = [
                'uid'=>$myuid,
                'touid'=>$uid,
                'id'=>['<=',$id],
            ];
            if (empty($id)) {
                unset(self::$map['a']['id'],self::$map['b']['id']);
            }
            if($maxid>0){
                self::$map['a']['id'] = ['>',$maxid];
                self::$map['b']['id'] = ['>',$maxid];
            }
            if($uid==0){    //系统消息
                self::$map['b'] = [];
            }
        }
        
        if ($msg_id || $msg_sys) {
            $_array = [];
            $msg_id && $_array['ext_id'] = $msg_id;
            $msg_sys && $_array['ext_sys'] = $msg_sys;

            self::$map['a'] = array_merge(self::$map['a'],$_array);            
            self::$map['b'] && self::$map['b'] = array_merge(self::$map['b'],$_array);
        }
        
        $data_list = self::where(function($query){
                $query->where(self::$map['a']);
            })->whereOr(function($query){
                $query->where(self::$map['b']);
            })->order("id desc")->paginate($rows);
        
        $array = getArray($data_list);
        foreach($array['data'] AS $key=>$rs){
            if($rs['id']>$maxid){
                $maxid = $rs['id'];
                if($rs['qun_id']>0){    //使用WS推消息后,下面的其实可以删除了
                    $qs = self::where(['qun_id'=>$rs['qun_id'],'uid'=>$myuid])->order('id desc')->find();
                    if($qs){
                        self::update(['id'=>$qs['id'],'visit_time'=>time()]);  //标志最后收到圈子群聊信息的时间
                    }
                }
            }
            //下面的其实可以删除了,因为在app\index\controller\wxapp\Msg中117行批量处理了
            if($rs['ifread']==0&&$rs['touid']==$myuid){  //标志为信息已读
                self::update(['id'=>$rs['id'],'ifread'=>1]);
            }
            $array['data'][$key] = self::format_msg($rs);
        }
        $array['lasttime'] = time()-$from_time; //对方最近操作的时间
        $array['maxid'] = $maxid;
        return $array;
    }
    
    /**
     * 把消息内容做转义处理, 发表信息成功的时候,要推消息也要用到.
     * @param array $rs
     * @return unknown
     */
    public static function format_msg($rs=[]){
        //$rs['content'] = str_replace(["\n",' '],['<br>','&nbsp;'],filtrate($rs['content']));
        $rs['content'] = self::format_content($rs['content']);
        $rs['content'] = fun("content@bbscode",$rs['content']);
        $rs['from_username'] = get_user_name($rs['uid']);
        $rs['from_icon'] = get_user_icon($rs['uid']);
        $rs['full_time'] = strtotime($rs['create_time']);
        $rs['time'] = format_time($rs['full_time'],true);
        return $rs;
    }
    
    
    /**
     * 获取消息用户列表
     * @param number $uid 当前登录用户的UID
     * @param number $rows
     * @param number $page
     */
    public static function get_listuser($uid=0,$rows=0,$page=0,$cache_time=0){
        if ($cache_time>0 && $page<2) {
            $listdb = cache("msg_listuser-{$uid}");
            if ($listdb) {
                foreach ($listdb AS $key=>$rs){
                    //$rs['new_num'] = 0;
                    $listdb[$key] = $rs;
                }
                return $listdb;
            }
        }
        $page>0 || $page=1;
        $min = ($page-1)*$rows;
        $subQuery = self::where('touid',$uid)->whereOr('uid',$uid)
        ->field('uid,touid,create_time,title,id,ifread,qun_id,visit_time,update_time')
        //->order('id desc')
        ->order('update_time desc,visit_time desc,id desc')
        ->limit(5000)   //理论上某个用户收到的私信及发出的私信及发出的圈子群聊(不包含圈子内其它人的聊天)很少有超过5千条。
        ->buildSql();
        
        $listdb = Db::table($subQuery.' a')
        ->field('uid,touid,create_time,title,id,qun_id,visit_time,update_time,count(id) AS num,sum(ifread) AS old_num,(qun_id*1000000 + uid + touid + POWER( uid, 1 /2 ) + POWER( touid, 1 /2 )) AS MX')
        ->group('MX')
        //->order('id','desc')
        ->order('update_time','desc')
        ->limit($min,$rows)
        ->select();
        
        $_data = Msguser::where('uid',$uid)->order('id desc')->limit($min,$rows)->column('aid,list');
        $array  = $_array = [];
        foreach($listdb AS $key=>$rs){
            $rs['new_num'] = 0;
            if($rs['qun_id']>0){
                $rs['f_uid'] = -$rs['qun_id'];
                $rs['title'] = '圈子群聊';
                $rs['qun'] = [];
                $visit_time = $rs['visit_time'];    //visit_time值,将会弃用
                if($_data[$rs['f_uid']]>$visit_time){
                    $visit_time = $_data[$rs['f_uid']];
                }
                $rs['new_num'] = self::where([
                    'qun_id'=>$rs['qun_id'],
                    'create_time'=>['>',$visit_time],
                ])->count('id');
                if($rs['new_num']>0){
                    $qs = getArray(self::where('qun_id',$rs['qun_id'])->order('id desc')->find());
                    $qs['content'] = get_word(del_html($qs['content']), 100);
                    $rs['id'] = $qs['id'];
                    $qs['username'] = get_user_name($qs['uid']);
                    $rs['qun'] = $qs;
                }else{
                    $rs['num'] = self::where('qun_id',$rs['qun_id'])->count('id');
                }
                $quninfo = fun('qun@getByid',$rs['qun_id']);
                if (empty($quninfo)) {
                    self::where('qun_id',$rs['qun_id'])->delete();
                    continue;
                }
                $rs['f_name'] = $quninfo['title'];
                $rs['f_icon'] = $quninfo['picurl'];
            }else{
                if($rs['uid']==$uid){
                    $rs['f_uid'] = $rs['touid'];
                    $rs['title'] = '对方还未回复...';
                }else{
                    $rs['f_uid'] = $rs['uid'];
                }
                if($rs['num']!=$rs['old_num']){ //无法确认是哪一方的未读消息,所以要进一步查询
                    $rs['new_num'] = self::where('touid',$uid)->where('uid',$rs['f_uid'])->where('ifread',0)->count('id');
                }
                if ($rs['f_uid']!=0) {
                    $rs['f_name'] = $rs['f_uid']<9999999?get_user_name($rs['f_uid']):'游客';
                    $rs['f_icon'] = $rs['f_uid']<9999999?get_user_icon($rs['f_uid']):'';
                }else{
                    $rs['f_name'] = '系统消息';
                }      
            }
             
            $rs['create_time'] = date('Y-m-d H:i',$rs['create_time']);
            $list = $rs['new_num']?$key+10000000000:($rs['update_time']?:$rs['visit_time']); //创建排序值,有新消息的固定排在前面
            $_array[$rs['f_uid']] = $list?:$rs['id'];
            $array[$rs['f_uid']] = $rs;
        }
                
        if ($_data) {
            $detail = $_array+$_data;
            arsort($detail);
            foreach($detail AS $_uid=>$value){
                if ( empty($array[$_uid]) ) {   //没有聊天记录的情况
                    $qun = [];
                    if ($_uid>0) {
                        $f_name = get_user_name($_uid);
                        $f_icon = get_user_icon($f_icon);
                        $title = '';
                        $qun_id = 0;
                        $num = $new_num = 0;
                        $content = '';
                        $username = '';
                    }else{
                        $title = '圈子群聊';
                        $qun_id = -$_uid;
                        $qun = fun('qun@getByid',$qun_id);
                        if (empty($qun)) {
                            Msguser::where('uid',$uid)->where('aid',$_uid)->delete();
                            continue;
                        }
                        $f_name = $qun['title'];
                        $f_icon = $qun['picurl'];
                        $num = 0;
                        
                        $new_num = self::where([
                            'qun_id'=>$qun_id,
                            'create_time'=>['>',$value],
                        ])->count('id');
                        if($new_num>0){
                            $qs = getArray(self::where('qun_id',$value)->order('id desc')->find());
                            $content = get_word(del_html($qs['content']), 100);
                            //$rs['id'] = $qs['id'];
                            $username = get_user_name($qs['uid']);
                            //$rs['qun'] = $qs;
                        }else{
                            $num = self::where('qun_id',$rs['qun_id'])->count('id');
                        }
                    }
                    $rs = [
                        'username'=>$username,
                        'qun_id'=>$qun_id,
                        'qun'=>$qun,
                        'title'=>$title,
                        'content'=>$content,
                        'create_time'=>format_time($value),
                        'f_uid'=>$_uid,
                        'f_name'=>$f_name,
                        'f_icon'=>$f_icon,
                        'num'=>$num,
                        'new_num'=>$new_num,
                    ];
                    $array[$_uid] = $rs;
                }
            }
        }        
        
        $listdb = array_values($array);
        if ($cache_time>0&&$page<2) {
            cache("msg_listuser-{$uid}",$listdb,$cache_time);
        }        
        return $listdb;
    }
    
    /**
     * 解析网址可以点击打开
     * @param string $content
     * @return mixed
     */
    private static function format_content($content=''){
        if(strstr($content,"<") && strstr($content,">")){    //如果是网页源代码的话，就不解晰了。
            return $content;
        }
        $content = filtrate($content);
        $content = preg_replace_callback("/(http|https):\/\/([\w\?&\.\/=-]+)/", array(self,'format_url'), $content);
        $content = str_replace(["\n"],['<br>'],$content);
        return $content;
    }
    
    private static function format_url($array=[]){
        return '<a href="'.$array[0].'" target="_blank">'.$array[0].'</a>';
    }
	
}
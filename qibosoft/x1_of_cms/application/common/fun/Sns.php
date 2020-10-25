<?php
namespace app\common\fun;
use think\Db;
use app\weibo\model\Feed;

class Sns{
    
    /**
     * 主题动态类型
     * @param string $type
     */
    public function type($type=''){
        $array = [
            'add'=>'发布',
            'comment'=>'评论',
            'reply'=>'回复',
            'fav'=>'收藏',
            'star'=>'推荐',
            'agree'=>'点赞',
        ];
        return $array[$type]?:$type;
    }
    
    /**
     * 把动态里边的每一条记录获取对应的数据
     * @param array $rs
     */
    public function get($rs=[]){
        static $info_array = [];
        if ($info_array[$rs['id']]) {       //同一条记录,避免重复调用此函数,而造成多次查询,另外也可以写入缓存处理
            return $info_array[$rs['id']];
        }
        if(empty($rs['sysid']) && empty($rs['aid'])){
            return ;
        }
        $array = modules_config($rs['sysid']);
        $class = "app\\{$array['keywords']}\\model\\Content";
        static $obj_array = [];
        $obj = $obj_array[$class];
        if(empty($obj)){
            if(class_exists($class)&&method_exists($class, 'getInfoByid')){
                $obj = $obj_array[$class] = new $class;
            }else{
                return ;
            }
        }
        $reply = [];
        $topic = $obj->getInfoByid($rs['aid'],true);        
        if($topic){
            $topic['sys_name'] = $array['name'];
            if (empty($topic['picurls'])) {
                $topic['picurls'] = Content::get_images($topic['full_content']);
            }
        }else{
            return [];
        }
        if($rs['type']=='comment'){
            $reply = query('comment_content',[
                    'where'=>['id'=>$rs['rid']],
                    'type'=>'one',
            ]);
        }elseif($rs['type']=='reply'){
            $reply = query($array['keywords'].'_reply',[
                    'where'=>['id'=>$rs['rid']],
                    'type'=>'one',
            ]);
        }
        
        $info_array[$rs['id']] = [$topic,$reply];
        return $info_array[$rs['id']];
    }
    
    /**
     * 自动订阅主题
     * @param unknown $info
     * @param number $time
     * @param string $msg
     * @return string
     */
    public function fav($info,$time=30,$msg='系统帮你订阅了本主题,下次本主题有回复,将会通知你'){
        $time = $time*1000;
        if (empty(modules_config('weibo'))) {
            return ;
        }
        $url = urls('weibo/api/fav_interest_topic',['sys'=>config('system_dirname'),'id'=>$info['id'],'uid'=>$info['uid']]);
        return "<script type='text/javascript'>
                setTimeout(function(){
                	var url='$url';
                	\$.get(url,function(res){
                		if(res.code==0){
                			layer.msg('$msg',{time:2500});
                		}
                	});
                },$time);
                </script>";
    }
    
    /**
     * 添加动态信息
     * @param unknown $array_uid 可以是数组，也可以是被访问者的UID
     * @param string $about 事件描述
     * @param string $type 事件分类,可以省略用默认的
     */
    public function add_msg($array_uid,$about='',$type='msg',$sysid=0,$aid=0,$rid=0){
        if (is_array($array_uid)) {
            $touid = $array_uid['uid'];
            $type = $array_uid['type'];
            $about = $array_uid['about'];
            $sysid = $array_uid['sysid'];
            $aid = $array_uid['aid'];
            $rid = $array_uid['rid'];
        }else{
            $touid = $array_uid;
        }
        $login_id = login_user('uid');
        if($touid != $login_id && !preg_match('/^([\d]+)/', $about)){
            $about = $login_id . "\t$about";
        }        
        $data=[
            'uid'   =>   $touid,
            'create_time'=>time(),
            'type'  =>   $type,
            'about' =>   $about,
            'sysid' =>   intval($sysid),
            'aid'   => intval($aid),
            'rid'   => intval($rid),
        ];
        $result = Feed::create($data);
        if($result && $touid != $login_id){
            $this->add_newmsgnum($touid);    //给被访问者的消息+1
        }
        return $result;
    }
    
    /**
     * 获取信息内容相关的用户
     * 主题与回复的所有用户 , 也包括关注主题的用户 但不包括自己
     * @param string $module
     * @param number $aid
     * @param string $type
     * @return mixed|PDOStatement|string|boolean|number
     */
    public function get_content_user($module='',$aid=0,$type='reply'){
        $m = modules_config($module);
        $listdb = [];
        if($type=='reply'){
            $listdb = Db::name($m['keywords'].'_reply')->where('aid',$aid)->group('uid')->column('uid');
        }elseif($type=='comment'){            
            $sysid = $m['id'];
            $listdb = Db::name('comment_content')->where('sysid',$sysid)->where('aid',$aid)->group('uid')->column('uid');
        }
        if($type!='add'){
            $listdb[] = Db::name($m['keywords'].'_content')->where('id',$aid)->value('uid');
        }
        
        $data = [];
        foreach($listdb AS $uid){
            $data['id'.$uid] = $uid;    //使用array_merge过滤重复,键必须是字符串才行,所以加个id,数字的话,不能过滤,只会增加
        }
        
        $_listdb = $this->get_fav_user($module,$aid);
        if($_listdb){
            $data = array_merge($data,$_listdb);
        }
        $uid = login_user('uid');
        unset($data['id'.$uid]);
        return $data;
    }
    
    /**
     * 获取对某个频道主题感兴趣的所有用户
     * @param string $module
     * @param number $aid
     * @return array
     */
    public function get_fav_user($module='',$aid=0,$name='weibo'){
        $m = modules_config($module);
        $sysid = $m['id'];
        $name || $name='weibo';
        $listdb = Db::name($name.'_fav')->where('sysid',$sysid)->where('aid',$aid)->column('uid');
        $data = [];
        foreach($listdb AS $uid){
            $data['id'.$uid] = $uid;     //使用array_merge过滤重复,键必须是字符串才行,所以加个id,数字的话,不能过滤,只会增加
        }
        return $data;
    }
    
    /**
     * 把新消息清0
     * @param number $uid
     */
    public function clear_msg($uid=0,$name='weibo'){
        if($uid!=login_user('uid')){
            return ;
        }
        $name || $name='weibo';
        Db::name($name.'_content1')->where('id',$uid)->update(['msgnum'=>0]);
    }
    
    /**
     * 检查用户是否有微博
     * @param number $uid
     */
    public function weibo($uid=0,$name='weibo'){
        $uid || $uid=login_user('uid');
        static $array=[];
        if(empty($array[$uid])){
            $name || $name='weibo';
            $array[$uid] = getArray(Db::name($name.'_content1')->where('uid',$uid)->find()); 
        }
        return $array[$uid];
    }
    
    
    /**
     * 动态新消息+1
     * @param number $uid
     */
    public function add_newmsgnum($uid=0,$name='weibo'){
        if($uid==login_user('uid')){    //自己就没有必要给自己增加新消息数目了
            return ;
        }
        $name || $name='weibo';
        Db::name($name.'_content1')->where('id',$uid)->setInc('msgnum',1);
    }
    
    /**
     * 将新产生的动态加入到当前用户的所有粉丝那里
     * 如果是主题回复的话,也发送到关注主题的用户那里
     * @param number $login_uid 当前用户的UID
     * @param array $data 博主动态索引
     * @return boolean
     */
    public function push_toFans($login_uid=0,$data=[],$name='weibo'){
        
        //$this->add_newmsgnum($login_uid);
        $name || $name='weibo';
        //获取当前用户的所有粉丝
        $listdb = query($name.'_member',[
                'where'=>['aid'=>$login_uid],
                'column'=>'uid',
        ]);
        
        if($data['sysid'] && $data['aid']){ //通过这里判断是发布信息内容 , 把信息里的回复用户及关注用户一起合并到粉丝用户那里增加动态
            $_topic = $this->get_content_user($data['sysid'],$data['aid'],$data['type']);
            if($_topic){
                $_listdb = [];
                foreach($listdb AS $_uid){
                    $_listdb['id'.$_uid] = $_uid;    //使用array_merge过滤重复,键必须是字符串才行,所以加个id,数字的话,不能过滤,只会增加
                }
                $listdb = array_merge($_listdb,$_topic);
            }
        }
        
        $array = [];
        foreach ($listdb AS $_uid){
            if($login_uid==$_uid){    //避免给自己再增加动态,因为钩子那里已经给自己加过动态了
                continue;
            }
            $this->add_newmsgnum($_uid);    //给他们都加一条新消息数量
            $array[] = array_merge($data,['uid'=>$_uid]);
        }
        
        $obj = new Feed();
        $obj->push_all($array);
        return true;
    }
    
    /**
     * 载入某个用户的所有频道的主题
     * @param number $uid 信息来源UID
     * @param number $touid 插入到某个用户
     * @return void|boolean
     */
    public function push_topic($uid=0,$touid=0){
        $touid || $touid=$uid;
        $data = [];
        $array = modules_config();
        foreach($array AS $rs){
            if($rs['keywords']=='weibo'){
                continue;
            }
            $class = "app\\{$rs['keywords']}\\model\\Content";
            if(class_exists($class)&&method_exists($class, 'getIndexByUid')){
                $obj = new $class;                
                $listdb = $obj->getIndexByUid($uid);
                foreach($listdb AS $vs){
                    $create_time = query("{$rs['keywords']}_content{$vs['mid']}",['where'=>['id'=>$vs['id']],'value'=>'create_time']);
                    $data[] = [
                            'aid'=>$vs['id'],
                            'sysid'=>$rs['id'],
                            'uid'=>$touid,
                            'create_time'=>$create_time,
                            'type'=>'add'
                    ];
                }                
            }
        }
        if (empty($data)) {
            return ;
        }
        $obj = new Feed();
        $obj->push_all($data);
        return true;
    }
    
    /**
     * 载入某个用户的所有频道的评论
     * @param number $uid 信息来源UID
     * @param number $touid 插入到某个用户
     * @param number $rows 只取最近多少条
     * @return void|boolean
     */
    public function push_comment($uid=0,$touid=0,$rows=100){
        $touid || $touid=$uid;
        $data = [];
        $listdb = query('comment_content',[
                'where'=>['uid'=>$uid],
                'order'=>'id desc',
                'limit'=>$rows,
                'column'=>'id,sysid,aid,create_time'
        ]);
        foreach($listdb AS $vs){
            $data[] = [
                    'aid'=>$vs['aid'],
                    'sysid'=>$vs['sysid'],
                    'uid'=>$touid,
                    'create_time'=>$vs['create_time'],
                    'type'=>'comment',
                    'rid'=>$vs['id'],
            ];
        }
        if (empty($data)) {
            return ;
        }
        $obj = new Feed();
        $obj->push_all($data);
        return true;
    }
    

    /**
     * 载入某个用户的论坛回复
     * @param number $uid 信息来源UID
     * @param number $touid 插入到某个用户
     * @param number $rows 只取最近多少条
     * @param string $type 数据表类型 默认是论坛频道,也有可能是把论坛复制为其它频道的话,要对应的修改
     * @return void|boolean
     */
    public function push_reply($uid=0,$touid=0,$rows=100,$type='bbs_reply'){
        $touid || $touid=$uid;
        $data = [];
        $listdb = query('bbs_reply',[
                'where'=>['uid'=>$uid],
                'order'=>'id desc',
                'limit'=>$rows,
                'column'=>'id,sysid,aid,create_time'
        ]);
        foreach($listdb AS $vs){
            $data[] = [
                    'aid'=>$vs['aid'],
                    'sysid'=>$vs['sysid'],
                    'uid'=>$touid,
                    'create_time'=>$vs['create_time'],
                    'type'=>'reply',
                    'rid'=>$vs['id'],
            ];
        }
        if (empty($data)) {
            return ;
        }
        $obj = new Feed();
        $obj->push_all($data);
        return true;
    }
    
}
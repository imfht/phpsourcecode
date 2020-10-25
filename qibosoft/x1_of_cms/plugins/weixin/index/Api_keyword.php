<?php
namespace plugins\weixin\index;

use plugins\weixin\model\WeixinAutoreply;
use plugins\weixin\model\User AS UserModel;
use plugins\weixin\model\WeixinMsg;

class Api_keyword extends Api
{
    //唯一入口
    public function execute(){
        parent::execute();          //不能缺少的，实现权限判断
        
        
        //这里是钩子扩展,优先级最高
        $array = [
                'keyword'=>$this->From_content,     //用户回复的关键字
                'wx_id'=>$this->user_appId,              //用户的微信唯一ID标志
                'user'=>$this->user,                          //用户的登录信息
                'user_token'=>$this->user_token,      //用户登录标志,传递给URL使用
        ];

		$result = $this->get_hook('weixin_mp_keyword',$array);
        if($result!==null){
            echo $this->give_text($result);
            exit;
        }

        $result = hook_listen('weixin_mp_keyword',$array,'',true);
        if ($result!='') {      //如果钩子有返回数据,就直接在这里输出,要终止掉下面的所有应用
            echo $this->give_text($result);
            exit;
        }        
        
        
        $this->run_model();     // 执行\plugins\weixin\libs\keyword 目录下面的PHP文件

        //上面的应用匹配到关键字后，最好die终止掉，不然这里会继续执行系统库里的关键字匹配，并且输出用户不想看到的不对应的信息
        $this->keyword_auto_reply();
        
        if(!empty($msg = $this->kefu_reply())){
            echo $this->give_text($msg);
        }
    }
    
    //关键字自动回复
    private function keyword_auto_reply(){
        $content = $this->From_content;
        if($content==''){
            return ;
        }
        $array = cache('weixin_aoto_reply_keyword');
        if(empty($array)){
            $array = WeixinAutoreply::get_keyword();
            cache('weixin_aoto_reply_keyword',$array);
        }
        foreach($array AS $key=>$value){
            if($content==$key || strstr($content,$key) ){
                $ts = WeixinAutoreply::get($value);
                if($ts['type']==1){	//图文信息
                    $_array = unserialize($ts['answer']);
                    $_arr = [];
                    if(is_array($_array)){
                        foreach($_array AS $_r){
                            $_arr[] = array(
                                'title'=>$_r['title'],
                                'picurl'=>tempdir($_r['pic']),
                                'about'=>$_r['desc'],
                                'url'=>$_r['link'],
                            );
                        }
                    }
                    if($_arr){
                        echo $this->give_news($_arr);
                        exit;
                    }
                }else{	//纯文本信息
                    if($ts['answer']!=''){
                        echo $this->give_text( $ts['answer'] );
                        exit;
                    }                    
                }
            }
        }
        return ;
    }
    
    //客服
    function kefu_reply($type=''){        
        if($this->webdb['webxin_type']<2){
            echo $this->give_text($this->webdb['weixin_problem']);	   //非认证帐号不能执行下面的操作
            exit;
        }
        $uid_array = [];
        if($this->webdb['weixin_reply_kefu']!=''){	     //后台设置了客服UID
            $detail = explode(' ',$this->webdb['weixin_reply_kefu']);
            foreach($detail AS $value){
                is_numeric($value) && $uid_array[] = intval($value);
            }
        }else{
            $array = UserModel::where('groupid',3)->column('uid,weixin_api,username');
            foreach ($array AS $rs){
                $rs['weixin_api'] && $uid_array[] = intval($rs['uid']);
            }
        }
        
        $uid_str = $uid_array?implode(',',$uid_array):0;
        if( in_array($this->user['uid'],$uid_array) ){	//公众号给客服或管理员发出的信息
            
            //判断使用了多少个标志符就代表给最近的哪个用户回复信息，标志符可以是空格
            if($this->webdb['weixin_reply_Tag']!='' && preg_match("/^{$this->webdb['weixin_reply_Tag']}/",$this->From_content)){
                $i=-1;
                while( preg_match("/^{$this->webdb['weixin_reply_Tag']}/",$this->From_content) ){
                    //一个空格代码第一个，两个空格就代表第二个用户，以此类推
                    $i++;
                    $this->From_content = substr($this->From_content,1);
                }
                $_SQL="$i,1";
                $ms = query("SELECT G.id,D.weixin_api FROM `".config('database.prefix')."weixinmsg` G LEFT JOIN `".config('database.prefix')."memberdata` D ON G.uid=D.uid WHERE G.uid NOT IN ($uid_str) ORDER BY G.id DESC LIMIT $_SQL ");
                if( !send_wx_msg($ms['weixin_api'],$this->From_content) ){
                    $MSG = "客户离开超过2天了，信息发送失败！";
                }
            }elseif($this->webdb['weixin_reply_Tag']!=''){
                $MSG = "客服回复用户信息请使用标志符“{$this->webdb['weixin_reply_Tag']}”，不然系统不知道你想给谁发信息";
            }else{
                $MSG = "请进后台设置标志符，不然系统不知道你想给谁发信息";
            }
            
        }else{
            //客户询问
            $lastvist = UserModel::where('uid','in',$uid_str)->order('lastvist','desc')->value('lastvist');            
            $this->webdb['weixin_reply_Time']>=1 || $this->webdb['weixin_reply_Time']=1;	//客服或管理员指定多久视为在线
            
            if(time()-$lastvist<$this->webdb['weixin_reply_Time']*3600*3 ){ 	//客服在线的情况下
                
                $array = UserModel::where('uid','in',$uid_str)->column('uid,weixin_api,username');
                $havesend = 0;
                foreach($array AS $rs){
                    
                    //部分客服如果离线2天就会发送不出信息，这里没做判断
                    if($type=='image'){
                        send_wx_msg($rs['weixin_api'],"来自“{$this->user['username']}”的图片");
                        send_wx_msg($rs['weixin_api'],'',array('type'=>'image','id'=>$this->MediaId));
                        
                    }elseif($type=='voice'){
                        send_wx_msg($rs['weixin_api'],"来自“{$this->user['username']}”的声音");
                        send_wx_msg($rs['weixin_api'],'',array('type'=>'voice','id'=>$this->MediaId));
                        
                    }elseif($type=='video'){
                        send_wx_msg($rs['weixin_api'],"来自“{$this->user['username']}”的短视频");
                        send_wx_msg($rs['weixin_api'],'',array('type'=>'video','id'=>$this->MediaId,'thumb_media_id'=>$this->ThumbMediaId));
                        
                    }else{
                        send_wx_msg($rs['weixin_api'],"“{$this->user['username']}”:{$this->From_content}");
                    }
                    $havesend++;
                }
                $MSG = $havesend ? '' : stripslashes($this->webdb['weixin_problem']); //给客服成功发出消息，就不需要再给客户发信息了。
            }else{
                $MSG = stripslashes($this->webdb['weixin_problem']);	//客服离线，请稍候，管理员会回复你的信息！
            }
        }
        
        if($type=='image'){
            $_type=1;
        }elseif($type=='voice'){
            $_type=2;
        }elseif($type=='video'){
            $_type=3;
        }elseif($type=='map'){
            $_type=4;
        }
        
        $data = [
                'fid'=>$ms['id'],
                'appid'=>$this->user_appId,
                'uid'=>$this->user['uid'],
                'posttime'=>time(),
                'content'=>filtrate($this->From_content),
                'type'=>$_type,
                'url'=>$this->MediaId,
        ];
        WeixinMsg::create($data);
        
        return $MSG;
    }
    
    
}
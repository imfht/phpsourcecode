<?php
//新粉丝关注
namespace plugins\weixin\index;


class Api_subscribe extends Api
{
    //唯一入口
    public function execute(){
        parent::execute();          //不能缺少的，实现权限判断
        $word = $this->EventKey;
        
        //这里是钩子扩展,优先级最高
        $array = [
                'keyword'=>$word,                           //扫码传进来的值
                'wx_id'=>$this->user_appId,              //用户的微信唯一ID标志
                'user'=>$this->user,                          //用户的登录信息
                'user_token'=>$this->user_token,      //用户登录标志,传递给URL使用
        ];

		$result = $this->get_hook('weixin_mp_subscribe',$array);
        if($result!==null){
            echo $this->give_text($result);
            exit;
        }
        
        $result = hook_listen('weixin_mp_subscribe',$array,'',true);
        if ($result!='') {      //如果钩子有返回数据,就直接在这里输出,要终止掉下面的所有应用
            echo $this->give_text($result);
            exit;
        }
        
        if(preg_match("/^qrscene_/",$word) ){	//扫码事件
            $word = str_replace('qrscene_','',$word);  //扫码传进来的值
            
            //这里是钩子扩展,优先级最高
            $array = [
                    'keyword'=>$word,                           //扫码传进来的值
                    'wx_id'=>$this->user_appId,              //用户的微信唯一ID标志
                    'user'=>$this->user,                          //用户的登录信息
                    'user_token'=>$this->user_token,      //用户登录标志,传递给URL使用
            ];

			$result = $this->get_hook('weixin_mp_scan',$array);
            if($result!==null){
                echo is_array($result)?$this->give_news($result):$this->give_text($result);
                exit;
            }
            
            $result = hook_listen('weixin_mp_scan',$array,'',true);
            if ($result!='') {      //如果钩子有返回数据,就直接在这里输出,要终止掉下面的所有应用
                echo is_array($result)?$this->give_news($result):$this->give_text($result);
                exit;
            }
            
        }
        
        $this->run_model();     //执行多个插件或模块里边的应用，方便扩展，当然也可以在这里写执行语句
        
        //上面的应用匹配到后，最好die终止掉，不然这里会继续执行系统库里的欢迎信息，并不是用户最想看的
        $msg = $this->subscribe_news();
        if ($msg!='') {
            echo $msg;
        }else{
            $msg = $this->subscribe_text();
            if ($msg!='') {
                echo $msg;
            }
        }
    }
    
    
    
    //新关注回复图文信息
    protected function subscribe_news(){
        if($this->webdb['weixin_welcome_title']!=''&&$this->webdb['weixin_welcome_link']!=''){
            $array = array(
                'title'=>$this->webdb['weixin_welcome_title'],
                'picurl'=>tempdir($this->webdb['weixin_welcome_pic']),
                'about'=>$this->webdb['weixin_welcome_desc'],
                'url'=>$this->webdb['weixin_welcome_link'],
            );
            return $this->give_news(array($array));
        }
    }
    
    //新关注回复的纯文本信息
    protected function subscribe_text(){
        $MSG = $this->webdb['weixin_welcome'];
        if($MSG!=''){	//纯文本回复
            //           if($this->webdb['weixin_type']<2){	//非认证号，不能使用客服接口！
            return $this->give_text($MSG);
            //             }else{
            //                 send_wx_msg($this->user_appId,$MSG);    //用客服接口的话，就可以跟图文信息不冲突
            //             }
        }
    }
    
    
}
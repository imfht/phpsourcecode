<?php
namespace plugins\weixin\index;


class Api_scan extends Api
{
    //唯一入口
    public function execute(){
        parent::execute();          //不能缺少的，实现权限判断
        $word = $this->EventKey;    //扫码传进来的值
        
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
        
        
        $this->run_model();     //执行多个插件或模块里边的应用，方便扩展，当然也可以在这里写执行语句
    }
    
}
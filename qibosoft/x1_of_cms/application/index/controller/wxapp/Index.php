<?php
namespace app\index\controller\wxapp;


use app\common\controller\IndexBase;

//小程序  
class Index extends IndexBase{
    
    public function base(){
        if (empty($this->user)) {
            $this->user = [];
        }else{
            unset($this->user['password'],$this->user['password_rand'],$this->user['qq_api'],$this->user['weixin_api'],$this->user['wxapp_api'],$this->user['unionid'],$this->user['config'],$this->user['rmb_pwd']);
        }
        $array = [
            'user'=>$this->user,
            'time'=>$this->request->time(),
            'admin'=>$this->admin,
        ];
        return $this->ok_js($array);
    }
    
    /**
     * 获取包含主题的模块
     * @return void|unknown|\think\response\Json
     */
    public function topic_mod(){
        $array = modules_config();
        $plugins = plugins_config();
        
        $data = [];
        $p_data = [];
        foreach ($array AS $rs){
            if( is_file(APP_PATH.$rs['keywords'].'/model/Content.php')&&!in_array($rs['keywords'],['exam']) ){
                $rs['module'] = 'module';
                $data[] = $rs;
            }
        }
        foreach ($plugins AS $rs){
            if(is_file(PLUGINS_PATH.$rs['keywords'].'/index/Quote.php') || is_file(PLUGINS_PATH.$rs['keywords'].'/model/Quote.php')){
                $rs['module'] = 'plugin';
                $p_data[] = $rs;
            }
        }
        $data=array_merge($data,$p_data);
         
        return $this->ok_js($data);
    }
}

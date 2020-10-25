<?php
//
class userAction extends frontendAction {
    

    //获取openid，建立用户表
    public function login(){

        $code = $this->_request('code','trim');
        try{
             
            $result=$this->GetWechatOpenId($code);
            //如果openid不存在，则保存openid，资料为空
            $user = D('user')->where(array('openid'=>$result['openid']))->find();
            if (!$user) {
                $user = D('user')->add(array('openid' => $result['openid']));
                $user = D('user')->where($result)->find();
            }
            $data['userid']=$user['id'];
            $data['username']=$user['username'];
            $data['avatar']=$user['avatar'];
        }catch (\Exception $e){
            $this->ajaxReturn(0,"获取失败",0);
        }
        $this->ajaxReturn(1,"获取成功",$data);
 
    }
    //去微信取用户
    function GetWechatOpenId($js_code){
        if (!$js_code) {
            throw new \Exception('code参数为null！');
        }
        //后期从系统获取
        //获取openid和session的地址
        //即 https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
        // $url=C('WECHAT_GET_OPEN_ID');
        //定义好的appid、appsecret等有关小程序配置的数组常量
        $url='https://api.weixin.qq.com/sns/jscode2session';
        $param=array();
        // $param[]='appid='.$wechat_data['appid'];
        // $param[]='secret='.$wechat_data['appsecret'];
        // $param[]='js_code='.$js_code;
        $param[]='appid=wx72c0cdca13512510';
        $param[]='secret=da3ad10eed825697c1be23dfda22f9d6';
        $param[]='js_code='.$js_code;
        $param[]='grant_type=authorization_code';
     
        $params=join('&',$param);
     
        $url=$url.'?'.$params;
      
        $result=$this->go($url,'post');
        

       $result=json_decode($result,true);
        return $result;
    }

    //去发送请求
    public function go($url,$method='POST',$data=''){
        if(!$url){
            return ;
        }
        if(!$method){
            return ;
        }
        $method=strtoupper($method);
        $header = array("Accept-Charset: utf-8"); 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
         
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }
 
 


     
     
 
 
}

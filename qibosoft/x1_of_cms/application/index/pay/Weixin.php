<?php
namespace app\index\pay;
use app\index\controller\Pay;
use app\index\model\Pay AS PayModel;

class Weixin extends Pay{
    
    /**
     * 提供给PC页面频繁的刷新是否已支付成功
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function checkpay(){
        $numcode = input('numcode');
        $info = getArray(PayModel::get(['numcode'=>$numcode]));
        if($info['ifpay']==1){
            return $this->ok_js([],'支付成功');
        }elseif(!$info){
            return $this->err_js('订单不存在');
        }else{
            $array = fun('weixin@check_order',$numcode);
            if(is_array($array) && $array['ispay']===true && $array['s_orderid']!=''){  //通过订单号来查询,避免丢单
                $result = $this->have_pay(preg_replace("/^000/", '', $numcode));   //000避免出现订单重复的现象,跟公众号那里有冲突
                if($result=='ok'){
                    PayModel::update([
                            'id'=>$info['id'],
                            's_orderid'=>$array['s_orderid'],
                    ]);
                    return $this->ok_js([],'支付成功');
                }
            }
            return $this->err_js('订单未支付'); 
        }
    }
    
    /**
     * 非微信访问,就展示付款二维码
     * @param unknown $array
     * @return mixed|string
     */
    protected function weixin_pay_inpc($array=[]){
        $data=[
                'money'=>$array['money'],
                'return_url'=>get_url('home'),      //手机付款完成之后,就跳到主页去算了
                'banktype'=>'weixin',
                'numcode'=>$array['numcode'],
        ];
        $qrcode = get_qrcode( get_url( post_olpay($data, false) ) );    //生成二维码给微信扫描,直接进入付款那一步
        
        //PC页面不停的刷新,判断到支付成功后,进行的页面回跳地址
        $return_url = input('return_url');
        $return_url .= strstr($return_url,'?') ? '&ispay=1' : '?ispay=1';
        
        $this->assign('qrcode',$qrcode);
        $this->assign('numcode',$array['numcode']);
        $this->assign('return_url',$return_url);
        $this->assign('money',$array['money']);
        $this->assign('title',$array['title']);
        return $this->fetch('weixin_pay_inpc');
    }
    
    //跳到付款页面,准备付款
    public function gotopay(){
        if(
                ($this->webdb['weixin_appid'] && $this->webdb['weixin_appsecret'] && $this->webdb['weixin_payid'] && $this->webdb['weixin_paykey'])
                ||
                ($this->webdb['wxapp_appid'] && $this->webdb['wxapp_appsecret'] && $this->webdb['wxapp_payid'] && $this->webdb['wxapp_paykey'])
        ){
            if(!$this->user){
//                 if( in_weixin() ){
//                     weixin_login($url='');
//                 }else{
//                     $this->error('请先登录!');
//                 }
            }else{
                //你当前帐号还没有绑定微信，不能使用微信支付
            }
        }else{
            $this->error('系统没有设置好微信支付接口,所以不能使用微信支付');
        }
        ;
        if(!in_weixin() && input('client_type')==''){   //不在微信端并且没有指定支付类型,就展示微信付款二维码
            $array = $this->olpay_send();
            return $this->weixin_pay_inpc($array);
        }else{
            if (input('client_type')=='') { //判断是不是在小程序中				
                return $this->fetch('choose_mp_wxapp');
            }
			$weixin_openid = $this->get_openid();    //获取当前微信的真实openid
            
            $array = $this->olpay_send();
            
            if (input('client_type')=='wxapp') {    //在微信小程序中支付
                $this->assign('array',$array);
                $this->assign('in_app',input('in_app')?1:0);    //是否在APP中访问
                return $this->fetch('wxapp_pay');
            }else{                //公众号支付
                include(ROOT_PATH.'plugins/weixin/api/jsapi.php');
            }
        }
    }
    
    /**
     * 获取当前微信的真实openid
     * 因为登录用户可能没绑定微信,也有可能是绑定了其它微信.所以这里要动态获取当前用户的真实openid
     */
    protected function get_openid(){
        $openid = get_cookie('weixin_openid');
        if ($openid!='') {
            return $openid;
        }
        $state = input('state');
        $code = input('code');
        
        if($state==1){            
            if(!$code){
                $this->error('code 值并不存在！');
            }            
            $string = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.config('webdb.weixin_appid').'&secret='.config('webdb.weixin_appsecret').'&code='.$code.'&grant_type=authorization_code');
            $array = json_decode($string,true);            
            $openid = $array['openid'];
            if($openid){
                set_cookie('weixin_openid',$openid,3600);
            }else{
                if($string == ''){
                    $this->error('获取微信接口内容失败，请确认你的服务器已打开 extension=php_openssl.dll ');
                }
                $this->error('openid 值不存在！错误详情如下：'.$string);
            }
        }else{
            $url = urlencode($this->weburl);
            header('location:https://open.weixin.qq.com/connect/oauth2/authorize?appid='.config('webdb.weixin_appid').'&redirect_uri='.$url.'&response_type=code&scope=snsapi_base&state=1#wechat_redirect');
            exit;
        }        
    }
    
    
    protected function send_config(){
        
        //微信接口，结尾加参数无效
        $array['wx_notify_url'] = $this->request->domain().url('pay/index',['banktype'=>'weixin','action'=>'back_notice','back_post'=>'wap']);
        $array['wx_return_url'] = $this->request->domain().url('pay/index',['banktype'=>'weixin','action'=>'pay_end_return']);
        $array['bankname'] = '微信支付';
        
        return $array;
    }
    
    /**
     * 付款完毕，跳转回来时执行的动作，用户看得到的操作界面
     */
    public function pay_end_return(){
        $ispay = input('ispay');
        $numcode = input('numcode');
        $return_url = $this->return_url;
        $return_url .= strstr($return_url,'?') ? '&' : '?';
        if($ispay=='ok'){
            $return_url .= 'ispay=1';
            $result = $this->have_pay($numcode,false);  //这里不能做操作，仅做检查,因为这个页面用户可以伪造
            if($result===1){
                $this->success('支付成功！订单已生效',$return_url); 
            }elseif($result===-1){
                $this->success('订单丢失，请联系管理员，请截图保留该订单号'.$return_url);
            }elseif($result===0){
                $this->success('订单还在处理中，请稍候！',$return_url);
            }           
        }else{
            $return_url .= 'ispay=0';
            $this->error('你并没有付款，订单不生效！',$return_url,[],3);
        }
    }
    
    /**
     * 付款成功后，微信后台通知，前台看不到的界面，只能用日志追踪
     * @return string
     */
    public function back_notice(){
        global $pay_end_data;
        $pay_end_data = '';
        if (input('client_type')=='wxapp') {    //小程序支付的情况
            config('webdb.weixin_appid', config('webdb.wxapp_appid') );    //小程序的appid要跟支付接口绑定
            config('webdb.weixin_payid', config('webdb.wxapp_payid') );
            config('webdb.weixin_paykey', config('webdb.wxapp_paykey') );
        }
        include(ROOT_PATH.'plugins/weixin/api/notify.php');
        
        if($pay_end_data['out_trade_no']){  //支付成功，才能得到这个订单号
            //$pay_end_data['attach']
            $result = $this->have_pay( preg_replace("/^000/", '', $pay_end_data['out_trade_no']) );   //000避免出现订单重复的现象,跟公众号那里有冲突
            if($result==-1){
                return '订单不存在';
            }elseif($result==1){
                return '已经支付过了';
            }elseif($result=='ok'){
                return 'ok';    //支付成功，这里所有的动作，前台都是不可见，只能单独写日志追踪
            }
        }
        return 'fail';
    }
    
}
<?php
namespace app\index\pay;
use app\index\controller\Pay;

class Alipay extends Pay{
    
    //跳到付款页面,准备付款
    public function gotopay(){
        
        if(!$this->webdb['wap_ali_id'] || !$this->webdb['wap_ali_partner'] || !$this->webdb['wap_ali_public_key']){
            $this->error('系统没有设置好支付宝wap接口,所以不能使用支付宝');
        }
        $array = $this->olpay_send();
        include(ROOT_PATH.'plugins/alipay/api/alipayapi.php');
    }
    
    
    protected function send_config(){
        
        //结尾加参数无效
        $array['ali_notify_url'] = $this->request->domain().url('pay/index',['banktype'=>'alipay','action'=>'back_notice','back_post'=>'wap']);
        $array['ali_return_url'] = $this->request->domain().url('pay/index',['banktype'=>'alipay','action'=>'pay_end_return']);
        $array['bankname'] = '支付宝';
        
        return $array;
    }
    
    //付款完毕，跳转回来时执行的动作，用户看得到的操作界面
    public function pay_end_return(){    
        $pay_end_data_numcode = '';
        include(ROOT_PATH.'plugins/alipay/api/return_url.php');
        $return_url = $this->return_url;
        $return_url .= strstr($return_url,'?') ? '&' : '?';
        
        if($pay_end_data_numcode){
            $return_url .= 'ispay=1';
            $result = $this->have_pay($pay_end_data_numcode);
            if($result===1){
                $this->success('已支付成功了',$return_url); 
            }elseif($result===-1){
                $this->success('订单丢失，请联系管理员，请截图保留该订单号'.$pay_end_data_numcode,$return_url);
            }elseif($result==='ok'){
                $this->success('支付成功！',$return_url);
            }           
        }else{
            $return_url .= 'ispay=0';
            $this->error('你并没有付款，订单不生效！',$return_url,[],3);
        }
    }
    
    //付款成功后，支付宝后台通知，前台看不到的界面，只能用日志追踪
    public function back_notice(){
        $pay_end_data_numcode = '';
        include(ROOT_PATH.'plugins/alipay/api/notify_url.php');   
        if($pay_end_data_numcode){  //支付成功，才能得到这个订单号
            $result = $this->have_pay($pay_end_data_numcode);
            if($result==-1){
                return '订单不存在';
            }elseif($result==1){
                //return '已经支付过了';
                return 'success';
            }elseif($result=='ok'){
                return 'success';    //后台返回给支付宝，这里所有的动作，前台都是不可见，只能单独写日志追踪
            }
        }
        return 'fail';
    }
    
}
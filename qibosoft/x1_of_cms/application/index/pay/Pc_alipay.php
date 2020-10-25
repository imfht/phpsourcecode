<?php
namespace app\index\pay;

use app\index\controller\Pay;
use app\index\model\Pay AS PayModel;

class Pc_alipay extends Pay{
    
    /**
     * 跳到付款页面,准备付款
     * @return mixed|string
     */
    public function gotopay(){
        
        if(!$this->webdb['alipay_service'] || !$this->webdb['alipay_partner'] || !$this->webdb['alipay_key']){
            $this->error('系统没有设置好支付宝接口,所以不能使用支付宝');
        }
        $array = $this->olpay_send();
        return $this->post_pay($array);
    }
    
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
            return $this->err_js('订单未支付');
        }
    }
    
    
    protected function send_config(){
        $content = input('content');
        //结尾加参数无效
        $array['ali_notify_url'] = $this->request->domain().url('pay/index',['banktype'=>'alipay','action'=>'back_notice','back_post'=>'pc']);
        $array['ali_return_url'] = $this->request->domain().url('pay/index',['banktype'=>'alipay','action'=>'pay_end_return']);
        $array['content'] = $content?$content:'PC在线充值';
        $array['bankname'] = '支付宝';
        
        return $array;
    }
    
    //付款完毕，跳转回来时执行的动作，用户看得到的操作界面
    public function pay_end_return(){   
        
        $notify_id = input('notify_id');
        $alipay_partner=config('webdb.alipay_partner');
        $veryfy_result = file_get_contents("http://notify.alipay.com/trade/notify_query.do?notify_id=$notify_id&partner=$alipay_partner");
        if(!preg_match("/true$/",$veryfy_result)){
            $this->error('安全验证参数校验失败<hr>'.$veryfy_result);
        }
        
        $pay_end_data_numcode = input('out_trade_no');
        
        $return_url = $this->return_url;
        $return_url .= strstr($return_url,'?') ? '&' : '?';
        
        if($pay_end_data_numcode){
            $return_url .= 'ispay=1';
            $result = $this->have_pay($pay_end_data_numcode);
            if($result===1){
                $this->success('已支付成功!!',$return_url); 
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
        
        $notify_id = input('notify_id');
        $alipay_partner = config('webdb.alipay_partner');
        $veryfy_result = file_get_contents("http://notify.alipay.com/trade/notify_query.do?notify_id=$notify_id&partner=$alipay_partner");
        
        if( !preg_match("/true/",$veryfy_result) ){
            return 'fail';
        }
        
        $pay_end_data_numcode = input('out_trade_no');
        
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
    
    private function post_pay($array){
        $url  = "https://mapi.alipay.com/gateway.do?";
        
        //支付宝的一些小BUG,要特别处理订单号
        //if(preg_match("/^0/",$array['numcode'])){
            //$array[numcode]="$array[numcode]code";
        //}
        
        $para = array(
                //'extra_common_param'=>$this->user['uid'],
                'notify_url'	=> $array['ali_notify_url'],
                
                '_input_charset' => 'utf-8',
                'service'		 => config('webdb.alipay_service'),	//交易类型
                'partner'		 => config('webdb.alipay_partner'),		//合作商户号
                'return_url'	 => $array['ali_return_url'],		//同步返回
                'payment_type'	 => 1,							//默认为1,不需要修改
                'quantity'		 => 1,							//商品数量,强制为1,总额在price处算好
                'subject'		 => $array['title'],			//商品名称，必填
                'body'			 => $array['content'],			//商品描述，必填
                'out_trade_no'	 => $array['numcode'],			//商品外部交易号，必填（保证唯一性）
                'price'		 => $array['money'],				//总额
                'seller_email'	 => config('webdb.alipay_id'),		//卖家邮箱，必填
                'logistics_fee'		=> '0.00',        			//物流配送费用
                'logistics_payment'	=> 'BUYER_PAY',   			//物流费用付款方式：SELLER_PAY(卖家支付)、BUYER_PAY(买家支付)、BUYER_PAY_AFTER_RECEIVE(货到付款)
                'logistics_type'	=> 'EXPRESS'    			//物流配送方式：POST(平邮)、EMS(EMS)、EXPRESS(其他快递)
        );
        ksort($para);
        
        $and='';
        foreach($para as $key => $value){
            if($value!==''){
                $_url  .= $and."$key=$value";
                $url  .= $and."$key=".urlencode($value);
                $and="&";
            }
        }
        
        $sign=md5($_url.config('webdb.alipay_key'));
        $url.="&sign=".$sign."&sign_type=MD5";
        
        //没有配置支付宝WAP版的话,就不考虑扫码支付!
       // if(!$this->webdb['wap_ali_id'] || !$this->webdb['wap_ali_partner'] || !$this->webdb['wap_ali_public_key']){
       //     echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=$url'>";
       //     exit;
        //}

        $this->assign('payurl',$url);
        
        $data=[
            'money'=>$array['money'],
            'return_url'=>get_url('home'),   //手机扫码付款完成之后,手机端就跳到主页去算了
            'banktype'=>'alipay',
            'numcode'=>$array['numcode'],
        ];
        $qrcode = get_qrcode( get_url( post_olpay($data, false) ) );    //生成二维码给支付宝扫描,直接进入付款那一步
        $this->assign('qrcode',$qrcode);
        
        //PC页面不停的刷新,判断到支付成功后,进行的页面回跳地址
        $return_url = input('return_url');
        $return_url .= strstr($return_url,'?') ? '&ispay=1' : '?ispay=1';
        $this->assign('return_url',$return_url);
        
        $this->assign('numcode',$array['numcode']);
        $this->assign('money',$array['money']);
        
        return $this->fetch('alipay');
    }
    
}
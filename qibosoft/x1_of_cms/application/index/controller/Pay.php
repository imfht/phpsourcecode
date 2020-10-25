<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use app\index\model\Pay AS PayModel;

/**
 * 在线支付,实质就是给用户的余额充值 , 所以money字段,没加密,用户恶意修改的话,就是少充点值, 不会给应用带来钻空子的问题
 * 所有应用都是根据可用余额来判断是否足够,再进行扣余额操作的
 * 详细说明 http://help.php168.com/711032
 */
class Pay extends IndexBase
{
    protected $return_url;  //支付成功后，返回到哪个地址
    
    protected function _initialize()
    {
        parent::_initialize();
        $return_url = input('return_url');
        if($return_url!=''){
            set_cookie('pay_return_url', $return_url);
        }else{
            $this->return_url = get_cookie('pay_return_url') ? get_cookie('pay_return_url') : '/';
        }
        libxml_disable_entity_loader(true);
    }
    

    
    /**
     * 支付入口
     * @param string $banktype 支付类型 微信支付weixin或支付宝支付alipay,或余额支付rmb
     * @param string $action 对应的动作行为
     * @param string $back_post 指定PC或WAP , 对于后台异步通知回调网址，必须要指定
     * @return mixed|string|unknown|mixed
     */
    public function index($banktype = '' , $action = '' , $back_post = '')
    {
        if ($banktype == '') {  //没有指定支付方式的话,就让用户自由选择支付方式
            if (empty($this->user)) {
                //$this->error('请先登录!');
            }
            $this->assign('weburl',$this->weburl);
            $this->assign('money',input('money'));
            return $this->fetch(input('iframe')?'pc_iframe_choose':'index');
            
        } elseif($banktype == 'rmb') {  //选择了余额支付
            
            if (empty($this->user)) {
                $this->error('请先登录!');
            }
            return $this->rmb_pay();
            
        } else {    //支付宝或微信支付的相关 支付前与支付后的操作
            
            $banktype = strtolower($banktype);
            
        }
        
        if ($action == '') {    //准备去支付, 支付成功的话,会有专门定义好的action
            $action = 'gotopay';
        }
        $class = '\\app\\index\\pay\\';
        
        $base = ucfirst($banktype); //微信或支付宝
       
        if(IN_WAP===true || $back_post=='wap'){  //在手机端,或者是强制指定了手机端
            $auto_name = 'Wap_'.$banktype;
        }else{
            $auto_name = 'Pc_'.$banktype;
        }
        if (class_exists($class.$auto_name)) {  //自适应PC或WAP
            $class .= $auto_name;
        }elseif(class_exists($class.$base)){
            $class .= $base;
        }else{
            $this->error('支付类型有误！');
        }
        if(!method_exists($class, $action)){
            $this->error('参数有误!');
        }
        $obj = new $class;
        return call_user_func_array([$obj, $action], []);
    }
    
    /**
     * 余额支付,这里没做支付密码的验证
     */
    protected function rmb_pay(){
        
        if ($this->user['rmb']<input('money')) {
            $this->error('你的余额不足!');
        }
        $callback_class = mymd5(urldecode(input('callback_class')),'DE');
        if ($callback_class){
            $this->run_callback($callback_class);
        }
        
        $return_url = input('return_url')?:$this->return_url;
        $return_url .= strstr($return_url,'?') ? '&' : '?';
        $return_url .= 'ispay=1';
        $this->success('支付成功！订单已生效',$return_url);
        //$this->redirect(input('return_url'));
    }
    
    
    //根据支付接口的实际要求，重新定义返回网址及其它相关参数,并且可以根据需要把olpay_send里边的参数覆盖
    protected function send_config(){
        $array = [];
        //结尾加参数无效
        //$array['背后通知网址'] = $this->request->domain().url('pay/index',['banktype'=>'支付方式','action'=>'back_notice']);
        //$array['返回网址'] = $this->request->domain().url('pay/index',['banktype'=>'支付方式','action'=>'pay_end_return']);
        //$array['bankname'] = '支付宝';
        
        return $array;
    }
    
    /**
     * 生成订单，准备支付
     *                              最好先指定订单号,可避免重复生成多余的订单
     * @return array
     */
    protected function olpay_send(){
        
        $money = input('money');
        //$banktype = input('banktype');
        $numcode = input('numcode'); 
        $title = input('title');
        
        $money = number_format($money,2);
        $money = str_replace(',','',$money);
        if($money<0.01){
            $this->error("充值金额不能小于0.01元");
        }
        
        $array['money'] = $money;
        $array['title'] = $title?$title:' 在线充值';
        $array['numcode'] = mymd5(urldecode($numcode),'DE') ?: 'n'.date('ymdHis').rands(3);
        
        $array = array_merge($array,$this->send_config());

        
        $data = [
                'numcode'=>$array['numcode'],
                'money'=>$array['money'],
                'posttime'=>time(),
                'uid'=>intval($this->user['uid']),
                'banktype'=>$array['bankname'],
                'callback_class'=>mymd5(urldecode(input('callback_class')),'DE'),     //支付成功后，后台执行的类
        ];

        $info = getArray( PayModel::get(['numcode'=>$array['numcode']]) );
        if(empty($info)){
            PayModel::create($data);
        }elseif($info['money']!=$array['money']){
            $this->error("金额有误!!!");
        }
        
        return $array;
    }
    
    /**
     * 支付成功后执行的，不能定义为public，否则会有漏洞
     * @param unknown $numcode 订单号
     * @param string $havepay 是否执行充值操作.
     * @return number|string
     */
    protected function have_pay($numcode,$havepay=true){
        
        $rt = PayModel::get(['numcode'=>$numcode]);
        if(!$rt){
            //$this->error('系统中没有您的充值订单，无法完成充值！');
            return -1;
        }
        
        if($rt['ifpay'] == 1){
            //$this->success("订单已充值成功",$fromurl);
            return 1;
        }
        
        if($havepay==true){   //如果仅是检查是否付款的，就不能执行以下操作，不然有漏洞
            PayModel::update(['ifpay'=>1,'id'=>$rt['id']]);            
            add_rmb($rt['uid'],$rt['money'],0,date('y年m月d日H:i ').'在线充值');
            //$this->success("恭喜你充值成功",$fromurl);
            $this->run_callback($rt['callback_class']);
			
			//扩展接口
			$this->get_hook('pay_end',$data=[],$rt);
            hook_listen('pay_end',$rt);   
			
            return 'ok';
        }else{
            return 0;
        }
    }
    
    /**
     * 支付成功,异步执行各种功能模块的应用 格式是 mymd5("app\\shop\\model\\Order@pay@5")
     * @param unknown $code
     */
    protected function run_callback($code){
        if (empty($code)) {
            return ;
        }
        //'app-shop-model-Order@pay@order_id|5' //弃用这种格式了
        $detail = explode('@',$code);
        $class = str_replace('-', '\\', $detail[0]);    //兼容以前用-隔开目录名的情况
        $action = $detail[1];
        $ar = explode('|',$detail[2]);
        //$_params = [$ar[0]=>$ar[1]];
        $_params = $ar;
        if(class_exists($class)&&method_exists($class,$action)){
            $obj = new $class;
            call_user_func_array([$obj, $action], $_params);
        }
    }
    
    /**
     * 微信支付接口调式
     */
    public function test(){
        
        
        print<<<EOT
 <html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
<script type="text/javascript" src="/public/static/js/core/jquery.min.js"></script>
                
    <title>微信支付</title>
                
 <script type="text/javascript">
    var wxpay = {};
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			wxpay,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				//alert(res.err_code+res.err_desc+res.err_msg);
				if(res.err_msg=='get_brand_wcpay_request:ok')window.location.href="https://x1.php168.com/index/pay/index/banktype/weixin/action/pay_end_return.html?ispay=ok&numcode=6e22a6621d";
				if(res.err_msg=='get_brand_wcpay_request:cancel')window.location.href="https://x1.php168.com/index/pay/index/banktype/weixin/action/pay_end_return.html?ispay=0&numcode=6e22a6621d";
			}
		);
	}
                
	function callpay()
	{
		$.get('/index.php/index/wxapp.pay/index.html?type=mp&money=0.02&'+Math.random(),function(res){
			if(res.code==0){
				wxpay = eval("("+res.data.json+")");
				if (typeof WeixinJSBridge == "undefined"){
					if( document.addEventListener ){
						document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
					}else if (document.attachEvent){
						document.attachEvent('WeixinJSBridgeReady', jsApiCall);
						document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
					}
				}else{
					jsApiCall();
				}
                
                
			}else{
				alert(res.msg);
			}
		});
                
	}
 </script>
                
 </head>
<body onload="callpay()">
    <br/>
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">0.01</span>元钱</b></font><br/><br/>
	<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
	</div>
</body>
</html>
EOT;
        
        
    }
}

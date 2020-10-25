<?php

namespace Muushop\Controller;

use Think\Controller;

class PayController extends Controller {

	protected $api_key;
    protected $app_id;
    protected $public_key;
    protected $rsa_key;
    protected $open_score_recharge;
    protected $open_balance_recharge;
    protected $open_withdraw;
        
	function _initialize()
	{
		//引入pingpay类库
        import('Pingpay.PingSDK.init',APP_PATH,'.php');
        $this->api_key=modC('MUUSHOP_PINGPAY_APIKEY','','Muushop');
        $this->app_id=modC('MUUSHOP_PINGPAY_APPID','','Muushop');
        $this->public_key=modC('MUUSHOP_PINGPAY_PUBLICKEY','','Muushop');
        $this->rsa_key=modC('MUUSHOP_PINGPAY_PRIVATEKEY','','Muushop');
	}

	public function charge(){
		header("Content-Type:text/html;charset=UTF-8");
        /*
        result_url：回调网址
        以上参数必须传过来，否则报错
        */
       	$order_no = I('order_no','','text');
        $result_url = I('result_url','','text');//支付成功后跳转回的地址,需要转码

        //判断参数以下参数是否传递
        if($amount && $channel){
             //发起pingpay在线支付
            $this->pingpay($order_no,$channel,$amount,'',$result_url);
        }else{
            //根据商户订单ID获取订单数据
            //模块开发约定：订单号字段命名：必须是order_no
            $order_info = D('Muushop/MuushopOrder')->where(array('order_no'=>$order_no))->find();
            if($order_info){
                $payment = $order_info['pay_type'];
                $amount = $order_info['paid_fee'];
                $channel = $order_info['channel'];
                
                if($payment == 'onlinepay'){//发起pingpay在线支付
                    $this->pingpay($order_no,$channel,$amount,$result_url);
                }
                if($payment == 'delivery'){//货到付款、直接跳转至成功页
                    $result_url = urldecode($result_url);
                    redirect($result_url);
                }
                if($payment == 'balance'){//余额支付

                }
            }else{
                $this->error('获取订单数据时发生错误！');
            }  
        }
    }

    /**
     * 通用在线支付确认页面
     * @return [type] [description]
     */
    public function payMent()
    {
        $confirmPage = I('confirmPage',0,'intval');//是否需要订单支付确认页
        $chid = I('data','','text');//由 Ping++ 生成的支付对象 ID， 27 位字符串
        $result_url = I('result_url','','text');
        
        \Pingpp\Pingpp::setPrivateKeyPath($this->rsa_key);

        \Pingpp\Pingpp::setApiKey($this->api_key);
        $ch = \Pingpp\Charge::retrieve($chid);
        
        //扫码支付判断
        if($ch['credential']['wx_pub_qr']){
            $credential = think_encrypt($ch['credential']['wx_pub_qr']);
        }
        if($ch['credential']['alipay_qr']){
            $credential = think_encrypt($ch['credential']['alipay_qr']);
        }
        //组装扫码支付参数
        $app=$ch['metadata']['module'];
        $table=$ch['metadata']['model'];
        $order=$ch['order_no'];
        
        //如果是扫码支付就直接跳转至二维码页面
        if($ch['credential']['wx_pub_qr'] || $ch['credential']['alipay_qr']){
             $this->redirect('muushop/pay/paybyqrcode',array('app'=>$app,'table'=>$table,'order_no'=>$ch['order_no'],'data'=>$credential,'result_url'=>$result_url),0, '页面跳转中...');
        }
        //非扫码支付处理
        $channel = D('Muushop/MuushopPay')->getPaychannelInfo($ch['channel']);//获取支付渠道的详细配置
        $ch_y = sprintf("%.2f",$ch['amount']/100);
        //是否启用订单确认支付页

        //$ch=json_decode($ch, true);
        //$this->assign('order',$order);
        $this->assign('confirmPage',$confirmPage);
        $this->assign('channel',$channel);//支付渠道
        $this->assign('ch_y',$ch_y);//金额转换成元
        $this->assign('ch',$ch);
        $this->display();
    }
    /**
     * 支付成功页
     * @return [type] [description]
     */
    public function success(){
    	$order_no = I('order_no','','text');
    	$this->display();
    }

    /**
     * 扫码支付页面
     * @param  string $app    应用
     * @param  string $model  订单数据表
     * @param  [type] $order_no 商家订单号
     * @param  [type] $data     加密的二维码url参数
     * @return [type]           [description]
     */
    public function payByQrcode($data)
    {
        $app = I('app','','text');
        $table = I('table','','text');
        $order_no = I('order_no','','text');
        $result_url = I('result_url','','text');
        $result_url = think_decrypt($result_url);//成功后的会跳地址
        $map['order_no']=$order_no;
        $order = M($table)->where($map)->find();
        if($order){

        }else{
            $this->error('参数错误');
        }
        $this->assign('result_url',$result_url);
        $this->assign('app',$app);
        $this->assign('table',$table);
        $this->assign('order_no',$order_no);
        //$this->assign('order',$order);
        $this->assign('data',$data);
        $this->display();
    }
    /**
     * 生成扫码支付二维码
     * @return [type] [description]
     */
    public function qrcode($data){
        $data = think_decrypt($data);
        $data = urldecode($data);
        $qrcode = qrcode($data,false,$picPath=false,$logo=false,$size='9',$level='L',$padding=2,$saveandprint=false);
    }
    
    /**
     * AJAS轮询支付状态
     * @param  string $app    应用
     * @param  string $model  订单数据表
     * @param  string $status 字段
     * @return [type]         [description]
     */
    public function payStatus($app,$table,$order_no,$status='paid')
    {
        $app = I('app','','text');
        $table = I('table','','text');
        $order_no = I('order_no','','text');

        $map['order_no'] = $order_no;
        $order = D($app.'/'.$table)->where($map)->find();

        if($order[$status]){
            $result['info']='已支付';
        }else{
            $result['info']='未支付';
        }
            $result['app']=$app;
            $result['table']=$table;
            $result['paid']=$order[$status];
            $result['order_no']=$order['order_no'];
        $this->ajaxReturn($result);
    }
    
    /**
     * 通过pingpay在线支付
     * @param  [type] $order_no   订单号
     * @param  [type] $channel    支付渠道
     * @param  [type] $amount     支付金额
     * @param  [type] $metadata   元数据（至少包含模块、模型、回调的方法）
     * @param  [type] $result_url 回调地址
     * @return [type]             [description]
     */
    private function pingpay($order_no,$channel,$amount,$result_url){
        //支付成功后的会跳地址
        $result_url = urldecode($result_url);
        //检查回调地址中是否包含?
        $check = strpos($result_url, '?'); 
        //如果存在
        if( $check !== false){
            $arr['success_url'] = $result_url;
        }else{//不存在
            $arr['success_url'] = $result_url;
        }
        
        $arr['product_id']=$order_no;//商品订单的id
        //获取特定渠道的额外参数
        $channel = strtolower($channel);
        $extra = $this->extra($channel,$arr);
        // 设置RSA私钥
        \Pingpp\Pingpp::setPrivateKeyPath($this->rsa_key);

        //发起支付 设置 API Key
        \Pingpp\Pingpp::setApiKey($this->api_key);
        // 支付参数
        $data['order_no'] = $order_no;//商户订单号,推荐使用 8-20 位，要求数字或字母，不允许其他字符
        $data['app'] = array('id'=>$this->app_id); //app [ id ]
        $data['channel'] = $channel;// 支付使用的第三方支付渠道取值，请参考：https://www.pingxx.com/api#api-c-new
        $data['amount'] = $amount; //订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
        $data['client_ip'] = $_SERVER['REMOTE_ADDR']; // 发起支付请求客户端的 IP 地址，格式为 IPV4，如: 127.0.0.1
        $data['currency'] = 'cny'; //三位 ISO 货币代码，目前仅支持人民币  cny 。
        $data['subject'] = '订单'.$order_no; //商品名称
        $data['body'] = '订单'.$order_no; //商品描述
        $data['extra'] = $extra;//特定渠道发起交易时需要的额外参数，以及部分渠道支付成功返回的额外参数
        $data['time_expire'] = '';//订单失效时间，用 Unix 时间戳表示。默认1天
        //系统约定通过metadata的module 来判断是哪个模块发起的支付请求
        //组装metadata
        //$data['metadata'] = $metadata; //使用键值对的形式来构建自己的 metadata，例如 metadata[color] = red，
        //$data['description'] = $order['description']; //订单附加说明，最多 255 个 Unicode 字符。

        try {
            $ch = \Pingpp\Charge::create($data);
            if($ch){
                $this->redirect('Muushop/pay/payMent',array('data'=>$ch['id'],'result_url'=>$result_url), 0, '页面跳转中...');
            }else{
                $check['error']['message'] = '支付参数有错误';
                $this->ajaxReturn($check);
            }
        } catch (\Pingpp\Error\Base $e) {
            // 捕获报错信息
            if ($e->getHttpStatus() != NULL) {
                header('Status: ' . $e->getHttpStatus());
                echo $e->getHttpBody();
            } else {
                echo $e->getMessage();
            }
        }
    }


    private function extra($channelName,$arr)
    {   
        $path = APP_PATH  . 'Pingpay/Conf/channel.php';
        $channel = load_config($path);
        //$extra = $channel[$channelName]['extra'];
        $extra = $this->_extra($channelName,$arr);
        return $extra;
    }

    private function _extra($channelName,$arr)//自定义extra的值
    {
        switch ($channelName)
        {
            case 'alipay':
              $extra = array(
                    //'extern_token'=>'',//开放平台返回的包含账户信息的 token（授权令牌，商户在一定时间内对支付宝某些服务的访问权限）。通过授权登录后获取的  alipay_open_id ，作为该参数的  value ，登录授权账户即会为支付账户，32 位字符串。
                    //'rn_check'=>'F',//是否发起实名校验，T 代表发起实名校验；F 代表不发起实名校验。
                    //'buyer_account'=>''//支付完成将额外返回付款用户的支付宝账号。
                );
            break;
            case 'alipay_wap':
              $extra= array(
                    'success_url'=>$arr['success_url'],//支付成功的回调地址。
                    'cancel_url'=>$arr['cancel_url'],//支付取消的回调地址， app_pay 为true时，该字段无效。
                    'app_pay'=>'true',//是否使用支付宝客户端支付，该参数为true时，调用客户端支付。
                    //'buyer_account'=>'',//支付完成将额外返回付款用户的支付宝账号。
                );
            break;
            case 'alipay_pc_direct':
            $extra = array(
                    'success_url'=>$arr['success_url'],//支付成功的回调地址。
                    'enable_anti_phishing_key'=>'',//是否开启防钓鱼网站的验证参数（如果已申请开通防钓鱼时间戳验证，则此字段必填)
                    'exter_invoke_ip'=>$_SERVER['REMOTE_ADDR'],//客户端 IP ，用户在创建交易时，该用户当前所使用机器的IP（如果商户申请后台开通防钓鱼IP地址检查选项，此字段必填，校验用）
                );
            break;
            case 'alipay_qr':
            $extra = array(
                    
                );
            break;
            case 'wx':
            $extra=array(
                    'limit_pay'=>'no_credit',//指定支付方式，指定不能使用信用卡支付可设置为  no_credit 
                    'goods_tag'=>$arr['goods_tag'],//商品标记，代金券或立减优惠功能的参数。
                    'open_id'=>$arr['open_id'],//用户在商户  appid 下的唯一标识
                    //'bank_type'=>'',//支付完成后额外返回付款用户的付款银行类型  bank_type
                );
            break;
            case 'wx_pub':
            $extra=array(
                    'limit_pay'=>'no_credit',//指定支付方式，指定不能使用信用卡支付可设置为  no_credit 。
                    'product_id'=>$arr['product_id'],//商品 ID，1-32 位字符串。此 id 为二维码中包含的商品 ID，商户自行维护。
                    'goods_tag'=>$arr['goods_tag'],//商品标记，代金券或立减优惠功能的参数。
                    //'open_id'=>'',//支付完成后额外返回付款用户的微信  open_id 。
                    //'bank_type'=>'',//支付完成后额外返回付款用户的付款银行类型  bank_type 。
                );
            break;
            case 'wx_pub_qr':
            $extra=array(
                    'limit_pay'=>'no_credit',//指定支付方式，指定不能使用信用卡支付可设置为  no_credit 。
                    'product_id'=>$arr['product_id'],//商品 ID，1-32 位字符串。此 id 为二维码中包含的商品 ID，商户自行维护。
                    'goods_tag'=>$arr['goods_tag'],//商品标记，代金券或立减优惠功能的参数。
                    //'open_id'=>'',//支付完成后额外返回付款用户的微信  open_id 。
                    //'bank_type'=>'',//支付完成后额外返回付款用户的付款银行类型  bank_type 。
                );
            break;
            case 'wx_wap':
            $extra=array(
                    'result_url'=>$arr['success_url'],//支付完成的回调地址。
                    'goods_tag'=>$arr['goods_tag'],//商品标记，代金券或立减优惠功能的参数。
                    //'open_id'=>'',//支付完成后额外返回付款用户的微信  open_id 。
                    //'bank_type'=>'',//支付完成后额外返回付款用户的付款银行类型  bank_type 。
                );
            break;
            case 'upacp_wap':
            $extra=array(
                    'result_url'=>$arr['success_url'],//支付完成的回调地址。
                );
            break;
            case 'upacp_pc':
            $extra=array(
                    'result_url'=>$arr['success_url'],//支付完成的回调地址。
                );
            break;
        }
        return $extra;
    }
    /**
     * 支付成功后的webhook处理
     * @return [type] [description]
     */
    public function webhooks(){

       if(IS_POST){
            $raw_data = file_get_contents('php://input');
            $headers = \Pingpp\Util\Util::getRequestHeaders();
            // 签名在头部信息的 x-pingplusplus-signature 字段
            $signature = isset($headers['X-Pingplusplus-Signature']) ? $headers['X-Pingplusplus-Signature'] : NULL;
            $result = $this->verify_signature($raw_data, $signature);
            //$result = 1;
            if ($result === 1) {
                // 验证通过
                echo 'verification success';
            } elseif ($result === 0) {
                http_response_code(400);
                echo 'verification failed';
                exit;
            } else {
                http_response_code(400);
                echo 'verification error';
                exit;
            }
            //接收到的处理事件
            $event = json_decode($raw_data, true);
            //在Ping++正确接收的webhoos回调后的处理
            //支付成功后处理
            if ($event['type'] == 'charge.succeeded') {
                //处理订单数据
                //获取订单号
                $data['id'] = $event['data']['object']['id'];
                $data['order_no']= $event['data']['object']['order_no'];
                $data['time_paid']= $event['data']['object']['time_paid'];
                
                $this->order_edit($data);
            }
            //退款成功后处理
            if ($event['type'] == 'refund.succeeded') {
                $refund = $event['data']['object'];
                // ...
                http_response_code(200); // PHP 5.4 or greater
            }
        }else{
            echo "数据有误";
            http_response_code(500);
        }
    }

    private function verify_signature($raw_data, $signature) 
    {
        $pub_key_contents = $this->public_key;
        // php 5.4.8 以上，第四个参数可用常量 OPENSSL_ALGO_SHA256
        return openssl_verify($raw_data, base64_decode($signature), $pub_key_contents, 'sha256');
    }

    private function order_edit($data){
        $order=D('Muushop/MuushopOrder')->get_order_by_order_no($data['order_no']);
        if($order['paid']!==1){//未支付状态就执行
            
            $wdata['id']=$order['id'];
            $wdata['paid']=1;
            $wdata['pingid']=$data['id'];
            $wdata['paid_time'] = $data['time_paid'];
            $wdata['status'] = 2;

            $res=D('Muushop/MuushopOrder')->add_or_edit_order($wdata);
            if(!$res){
                echo '数据写入失败';
                http_response_code(500);
            }else{
                //支付成功后的数据处理
                echo '支付状态更新成功';
                http_response_code(200);
            }
        }else{
            echo '数据有误或已处理';
            http_response_code(500);
        }
    }
}
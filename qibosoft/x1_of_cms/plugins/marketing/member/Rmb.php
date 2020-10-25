<?php
namespace plugins\marketing\member;

use app\common\controller\MemberBase; 
use plugins\marketing\model\RmbGetout;
//use plugins\marketing\model\RmbInfull;
use plugins\marketing\model\RmbConsume;

class Rmb extends MemberBase
{
    /**
     * 充值、消费记录
     * @return mixed|string
     */
    public function index(){	    
		$map = [
                'uid'=>$this->user['uid']
        ];
		$data_list = RmbConsume::where($map)->order("id desc")->paginate(10);
		$data_list->each(function($rs,$key){
		    $rs['title'] = del_html($rs['about']);
		    return $rs;
		});
		$pages = $data_list->render();
		$listdb = getArray($data_list)['data'];
		//给模板赋值变量
		$this->assign('pages',$pages);
		$this->assign('listdb',$listdb);		
		return $this->pfetch();
	}
	
	/**
	 * 删除充值消费记录
	 */
	public function delete($id)
	{
	    if (RmbConsume::destroy([$id])) {
	        $this->success('删除成功','index');
	    }else{
	        $this->error('删除失败');
	    }
	}
    

	/**
	 * 充值
	 * @param string $callback_url 指定回调地址
	 * @param string $type 指定充值方式, weixin alipay
	 * @return mixed|string
	 */
	public function add($callback_url='',$type='',$rmb=0){
	    if(IS_POST){
	        $data = $this->request->post();
	        if ( $data['money']<0.01 ) {
	            $this->error('充值金额不能小于0.01元');
	        }
	        $numcode = 'r'.date('ymdHis').rands(3);      //订单号
	        //直接跳转支付
	        post_olpay([
	            'money' => $data['money'],
	            'return_url' => $callback_url?:purl('index'),
	            'banktype' => $type,//in_weixin() ? 'weixin' : 'alipay',
	            'numcode' => $numcode,
	            'callback_class' => '',
	        ] , true);	
	    }
	    $this->assign('rmb',$rmb);
	    return $this->pfetch();
	}
	
	/**
	 * 提现
	 * @return unknown
	 */
	public function getmoney(){
	    
	    if (empty($this->user['rmb_pwd'])) {
	        $this->success('你还没有设置支付密码,请先设置支付密码!','pwd');;
	    }
	    
	    $array = is_array($this->webdb['getout_percent_money'])?$this->webdb['getout_percent_money']:json_decode($this->webdb['getout_percent_money'],true);
	    $getout_percent_money = $array[$this->user['groupid']];
	    
	    if ($this->webdb['getout_need_join_mp']) {
	        if (empty($this->user['weixin_api'])) {
	            $this->error("你还没有绑定微信不能提现，请点击下一步先绑定微信",url('member/bindlogin/weixin'));
	        }elseif(empty($this->user['wx_attention']) && wx_check_attention($this->user['weixin_api'])!==true ){
	            $imgshow = '<img style="max-width:50%;" src="'.tempdir($this->webdb['mp_code_img']).'">';
	            $this->error("请先关注公众号才可以提现，关注公众号方便接收提现申请动态!<br>$imgshow");
	        }
	    }
	    if($this->webdb['getout_need_yzphone']){
	        if (empty($this->user['mobphone'])||empty($this->user['mob_yz'])) {
	            $this->error("你还没有绑定手机号不能提现，请点击下一步先绑定手机号才能提现",url('member/yz/mob'));
	        }
	    }
	    
	    if(IS_POST){	        
	        $data = $this->request->post();	        
	        if($this->user['rmb']<0.3){
	            $this->error("你当前可用余额小于0.3元,无法提现!");
	        }
	        $min_money = $this->webdb['min_getout_money'] ?: 50 ; //最低提现金额
	        if(!$data['banktype']){
	            $this->error("请选择一个收款帐号!");
	        }elseif($data['money'] > $this->user['rmb']){
	            $this->error("提现金额不能大于你的可用余额");
	        }elseif($data['money']<$min_money){
	            $this->error("提现金额不能小于 {$min_money} 元");
	        }elseif($data['money']<0.01){
	            $this->error("提现金额不能小于 0.01 元");
	        }elseif($this->user['rmb_pwd'] && $this->user['rmb_pwd']!=md5($data['pwd'])){
	            $this->error("支付密码不对!");
	        }
	        
	        if ($this->webdb['getout_rmb_tn']>0) {
	            $time = time()-$this->webdb['getout_rmb_tn']*3600*24;
	            $total = RmbConsume::where( 'uid',$this->user['uid'] )->where( 'money','>',0 )->where( 'posttime','>',$time)->sum('money');
	            if ($total > $this->user['rmb']-$data['money'] ) {
	                $_rmb = $this->user['rmb']-$total;
	                $this->error('很抱歉，你本次最多只能提现 '.($_rmb>0?$_rmb:0).' 元，系统设置的T+N提现周期是 '.$this->webdb['getout_rmb_tn'].' 天，剩余的款项请过几天后再申请提现！');
	            }
	        }
	        $data['real_money'] = $data['money'];          //标志实际申请提现金额,即没扣手续费前的
	        if($getout_percent_money>0){    //扣除手续费	            
	            $data['money'] = $data['money'] - $data['money'] * $getout_percent_money;
	        }
	        $data['uid'] = $this->user['uid'];
	        $data['username'] = $this->user['username'];
	        $data['posttime'] = time();
	        if ( RmbGetout::create($data) ) {
	            add_rmb($this->user['uid'],-$data['real_money'],$data['real_money'],'申请提现冻结');
	            send_admin_msg('有人申请提现了',$this->user['username'].' 申请提现 '.$data['money'].' 元,请尽快审核处理');
	            $this->success('你的信息已提交，请耐心等候审核，我们将于3个工作日内处理(如遇节假日会延长)，请注意查收短消息。如有疑问请联系客服',auto_url('marketing/rmb/index'));
	        } else {
	            $this->error('提现失败');
	        }
	    }
	    
	    $cfg = unserialize($this->user['config']);
	    $detail = explode("\r\n",$cfg['bank']);
	    foreach($detail AS $key=>$value){
	        if (empty($value)) {
	            unset($detail[$key]);
	        }
	    }
	    $this->assign('alipay_id',$cfg['alipay_id']);
	    $this->assign('listdb',$detail);
	    $this->assign('getout_percent_money',$getout_percent_money*100);   //提现手续费
	    return $this->pfetch();
	}
	
	/**
	 * 提现记录
	 * @return mixed|string
	 */
	public function log(){
	    $listdb = RmbGetout::where('uid',$this->user['uid'])->order('id','desc')->paginate(20);
	    $listdb->each(function($rs,$key){
	        $rs['posttime'] = date('Y-m-d H:i',$rs['posttime']);
	        $rs['replytime'] = $rs['replytime']?date('Y-m-d H:i',$rs['replytime']):'';
	        return $rs;
	    });
	    $pages = $listdb->render();	    
	    $this->assign('listdb',$listdb);
	    $this->assign('pages',$pages);	    
	    return $this->pfetch();
	}
	
	/**
	 * 收款帐号设置
	 * @return mixed|string
	 */
	public function edit(){
	    $cfg = unserialize($this->user['config']);
	    if(IS_POST){	        
	        $data = $this->request->post();
	        $cfg['alipay_id'] =  $data['alipay_id'];
	        $cfg['bank'] =  $data['bank'];
	        $cfg = serialize($cfg);
	        $array = [
	                'uid'=>$this->user['uid'],
	                'config'=>$cfg,
	        ];
	        if ( edit_user($array) ) {
	            $this->success('更新成功');
	        } else {
	            $this->error('更新失败');
	        }
	    }
	    $this->assign('cfg',$cfg);
	    return $this->pfetch();
	}
	
	/**
	 * 修改余额支付密码
	 * @return mixed|string
	 */
	public function pwd(){
	    if(IS_POST){
	        $data = $this->request->post();
	        if($data['pay_pwd']==''){
	            $this->error('新密码不能为空');
	        }elseif($data['pay_pwd']!=$data['pay_pwd2']){
	            $this->error('两次输入密码不一致');
	        }elseif($this->user['rmb_pwd'] && $this->user['rmb_pwd']!=md5($data['old_pwd'])){
	            $this->error('原密码不正确');
	        }
	        $array = [
	                'uid'=>$this->user['uid'],
	                'rmb_pwd'=>md5($data['pay_pwd']),
	        ];
	        if ( edit_user($array) ) {
	            $this->success('更新成功');
	        } else {
	            $this->error('更新失败');
	        }
	    }
	    return $this->pfetch();
	}
	
	/**
	 * 重置支付密码
	 * @param string $type
	 * @return void|\think\response\Json|void|unknown|\think\response\Json
	 */
	public function getpwd($type='phone'){
	    //邮箱注册码与手机注册码,不建议同时启用,所以这里没分开处理
	    if( time()-get_cookie('send_num') <60 ){
	        return $this->err_js('1分钟后,才能再次获取验证码!');
	    }elseif( time()-cache('send_num'.md5(get_ip()))<60){
	        return $this->err_js('1分钟后,当前IP才能再次获取验证码!');
	    }
	    $num = cache(get_cookie('user_sid').'_pwd') ?: rand(1000,9999);
	    $send_num = $num;
	    //$send_num = get_md5_num($to.$num,6);
	    $title = '来自《'.config('webdb.webname').'》的支付密码,请注意查收';
	    $content = '你的支付密码是:'.$send_num;
	    cache(get_cookie('user_sid').'_pwd',$num,600);
	    if($type=='phone'){
	        $result = send_sms($this->user['mobphone'],$send_num);
	    }elseif($type=='email'){
	        $result = send_mail($this->user['email'],$title,$content);
	    }else{
	        $result = '请选择类型!';
	    }
	    if($result===true){
	        edit_user([
	                'uid'=>$this->user['uid'],
	                'rmb_pwd'=>md5($send_num),
	        ]);
	        set_cookie('send_num', time());
	        cache('send_num'.md5(get_ip()),time(),100);
	        return $this->ok_js();
	    }else{
	        return $this->err_js($result);
	    }
	}
}

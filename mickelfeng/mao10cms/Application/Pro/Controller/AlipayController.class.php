<?php
namespace Pro\Controller;
use Think\Controller;
class AlipayController extends Controller {
    public function alipay2(){
    	if(!$_POST['buyer_name']) {
	    	$this->error('请填写收货人姓名');
    	};
    	if(!$_POST['buyer_city']) {
	    	$this->error('请选择省份和城市');
    	};
    	if(!$_POST['buyer_address']) {
	    	$this->error('请填写详细地址');
    	};
    	if(!$_POST['buyer_phone']) {
	    	$this->error('请填写联系电话');
    	};
		require_once(THINK_PATH."../pay/alipay2/alipayapi.php");
    }
    public function alipay2_return(){
    	$user_ids = M('meta')->where("meta_key='user_level' AND meta_value='10' AND type = 'user'")->getField('page_id',true);
    	foreach($user_ids as $val) :
    	mc_mail(mc_get_meta($val,'user_email',true,'user'),'网站消息','您的网站有新的订单，请到管理员后台查看。');
    	endforeach;
    	require_once(THINK_PATH."../pay/alipay2/return_url.php");
    }
    public function alipay2_notify(){
    	require_once(THINK_PATH."../pay/alipay2/notify_url.php");
    }
    public function alipay(){
    	if(!$_POST['buyer_name']) {
	    	$this->error('请填写收货人姓名');
    	};
    	if(!$_POST['buyer_city']) {
	    	$this->error('请选择省份和城市');
    	};
    	if(!$_POST['buyer_address']) {
	    	$this->error('请填写详细地址');
    	};
    	if(!$_POST['buyer_phone']) {
	    	$this->error('请填写联系电话');
    	};
		require_once(THINK_PATH."../pay/alipay/alipayapi.php");
    }
    public function alipay_return(){
    	$user_ids = M('meta')->where("meta_key='user_level' AND meta_value='10' AND type = 'user'")->getField('page_id',true);
    	foreach($user_ids as $val) :
    	mc_mail(mc_get_meta($val,'user_email',true,'user'),'网站消息','您的网站有新的订单，请到管理员后台查看。');
    	endforeach;
    	require_once(THINK_PATH."../pay/alipay/return_url.php");
    }
    public function alipay_notify(){
    	require_once(THINK_PATH."../pay/alipay/notify_url.php");
    }
    public function alipay_wap(){
    	if(!$_POST['buyer_name']) {
	    	$this->error('请填写收货人姓名');
    	};
    	if(!$_POST['buyer_city']) {
	    	$this->error('请选择省份和城市');
    	};
    	if(!$_POST['buyer_address']) {
	    	$this->error('请填写详细地址');
    	};
    	if(!$_POST['buyer_phone']) {
	    	$this->error('请填写联系电话');
    	};
		require_once(THINK_PATH."../pay/alipay_wap/alipayapi.php");
    }
    public function alipay_wap_return(){
    	$user_ids = M('meta')->where("meta_key='user_level' AND meta_value='10' AND type = 'user'")->getField('page_id',true);
    	foreach($user_ids as $val) :
    	mc_mail(mc_get_meta($val,'user_email',true,'user'),'网站消息','您的网站有新的订单，请到管理员后台查看。');
    	endforeach;
    	require_once(THINK_PATH."../pay/alipay_wap/call_back_url.php");
    }
    public function alipay_wap_notify(){
    	require_once(THINK_PATH."../pay/alipay_wap/notify_url.php");
    }
    //财付通
    public function tenpay(){
    	if(!$_POST['buyer_name']) {
	    	$this->error('请填写收货人姓名');
    	};
    	if(!$_POST['buyer_city']) {
	    	$this->error('请选择省份和城市');
    	};
    	if(!$_POST['buyer_address']) {
	    	$this->error('请填写详细地址');
    	};
    	if(!$_POST['buyer_phone']) {
	    	$this->error('请填写联系电话');
    	};
    	require_once(THINK_PATH."../pay/tenpay/tenpay.php");
    }
    public function tenpay_return(){
    	$user_ids = M('meta')->where("meta_key='user_level' AND meta_value='10' AND type = 'user'")->getField('page_id',true);
    	foreach($user_ids as $val) :
    	mc_mail(mc_get_meta($val,'user_email',true,'user'),'网站消息','您的网站有新的订单，请到管理员后台查看。');
    	endforeach;
    	require_once(THINK_PATH."../pay/tenpay/payReturnUrl.php");
    }
    public function tenpay_notify(){
    	require_once(THINK_PATH."../pay/tenpay/payNotifyUrl.php");
    }
    public function hdfk(){
    	if(mc_user_id()) {
    		if(!$_POST['buyer_name']) {
		    	$this->error('请填写收货人姓名');
	    	} elseif(!$_POST['buyer_city']) {
		    	$this->error('请选择省份和城市');
	    	} elseif(!$_POST['buyer_address']) {
		    	$this->error('请填写详细地址');
	    	} elseif(!$_POST['buyer_phone']) {
		    	$this->error('请填写联系电话');
	    	} else {
    			$now = strtotime("now");
				$cart = M('action')->where("user_id='".mc_user_id()."' AND action_key='cart'")->select();
				if($cart) {
					$action['date'] = $now;
					$action['action_key'] = 'wait_hdfk';
					M('action')->where("user_id='".mc_user_id()."' AND action_key='cart'")->save($action);
					M('action')->where("user_id='".mc_user_id()."' AND action_key='address_pending'")->delete();
					M('action')->where("user_id='".mc_user_id()."' AND action_key='trade_pending'")->delete();
					$action['page_id'] = mc_user_id();
					$action['user_id'] = mc_user_id();
					$action['action_key'] = 'address_wait_hdfk';
					$action['action_value'] = '<h4>'.I('param.buyer_name').'</h4><p>'.I('param.buyer_province').'，'.I('param.buyer_city').'，'.I('param.buyer_address').'</p><p>'.I('param.buyer_phone').'</p>';
					M('action')->data($action)->add();
					$action['action_key'] = 'trade_wait_hdfk';
					$action['action_value'] = $out_trade_no;
					M('action')->data($action)->add();
					$id = mc_user_id();
					mc_delete_meta($id,'buyer_name','user');
					if(I('param.buyer_name')) {
						mc_add_meta($id,'buyer_name',I('param.buyer_name'),'user');
					};
					mc_delete_meta($id,'buyer_province','user');
					if(I('param.buyer_province')) {
						mc_add_meta($id,'buyer_province',I('param.buyer_province'),'user');
					};
					mc_delete_meta($id,'buyer_city','user');
					if(I('param.buyer_city')) {
						mc_add_meta($id,'buyer_city',I('param.buyer_city'),'user');
					};
					mc_delete_meta($id,'buyer_address','user');
					if(I('param.buyer_address')) {
						mc_add_meta($id,'buyer_address',I('param.buyer_address'),'user');
					};
					mc_delete_meta($id,'buyer_phone','user');
					if(I('param.buyer_phone')) {
						mc_add_meta($id,'buyer_phone',I('param.buyer_phone'),'user');
					};
					foreach($cart as $val) {
						//库存、销量
				        $kucun = mc_get_meta($val['page_id'],'kucun')-1;
				        mc_update_meta($val['page_id'],'kucun',$kucun);
				        $xiaoliang = mc_get_meta($val['page_id'],'xiaoliang')+1;
				        mc_update_meta($val['page_id'],'xiaoliang',$xiaoliang);
			        };
					$this->success('货到付款订单提交成功！',U('User/index/pro?id='.mc_user_id()));
					$user_ids = M('meta')->where("meta_key='user_level' AND meta_value='10' AND type = 'user'")->getField('page_id',true);
			    	foreach($user_ids as $val) :
			    		mc_mail(mc_get_meta($val,'user_email',true,'user'),'网站消息','您的网站有新的订单，请到管理员后台查看。');
			    	endforeach;
				} else {
					$this->error('购物车里没有任何商品！');
				};
    			
	    	};
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    public function alipay2_wish(){
    	require_once(THINK_PATH."../pay/alipay2/alipayapi_wish.php");
    }
    public function alipay2_return_wish(){
    	require_once(THINK_PATH."../pay/alipay2/return_url_wish.php");
    }
    public function alipay2_notify_wish(){
    	require_once(THINK_PATH."../pay/alipay2/notify_url_wish.php");
    }
    public function alipay_wish(){
    	require_once(THINK_PATH."../pay/alipay/alipayapi_wish.php");
    }
    public function alipay_return_wish(){
    	require_once(THINK_PATH."../pay/alipay/return_url_wish.php");
    }
    public function alipay_notify_wish(){
    	require_once(THINK_PATH."../pay/alipay/notify_url_wish.php");
    }
    public function alipay_wap_wish(){
    	require_once(THINK_PATH."../pay/alipay/alipayapi_wish.php");
    }
    public function alipay_wap_return_wish(){
    	require_once(THINK_PATH."../pay/alipay_wap/call_back_url_wish.php");
    }
    public function alipay_wap_notify_wish(){
    	require_once(THINK_PATH."../pay/alipay_wap/notify_url_wish.php");
    }
    //财付通
    public function tenpay_wish(){
    	require_once(THINK_PATH."../pay/tenpay/tenpay_wish.php");
    }
    public function tenpay_return_wish(){
    	require_once(THINK_PATH."../pay/tenpay/payReturnUrl_wish.php");
    }
    public function tenpay_notify_wish(){
    	require_once(THINK_PATH."../pay/tenpay/payNotifyUrl_wish.php");
    }
}
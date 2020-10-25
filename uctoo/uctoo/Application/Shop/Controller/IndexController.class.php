<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2015 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace Shop\Controller;

use Think\Controller;
use Shop\Model\ShopOrderModel as ShopOrderModel;
use Com\Wxpay\example\JsApiPay;
use Com\Wxpay\example\NativePay;
use Com\Wxpay\lib\WxPayApi;
use Com\Wxpay\lib\WxPayConfig;
use Com\Wxpay\lib\WxPayUnifiedOrder;
use Com\Wxpay\lib\WxPayOrderQuery;
use Com\TPWechat;
class IndexController extends Controller {
	protected $product_cats_model;
	protected $product_model;
	protected $order_model;
	protected $order_logic;
	protected $coupon_model;
	protected $cart_model;
	protected $user_id;
	protected $coupon_logic;
	protected $message_model;
	protected $user_coupon_model;
	protected $user_address_model;
	protected $product_comment_model;

	protected $weObj;
	protected $mp_id;//公众号mp_id
	protected $wx_id;//微信会员id
	function _initialize()
	{
		$this->product_cats_model = D('Shop/ShopProductCats');
		$this->product_model      = D('Shop/ShopProduct');
		$this->order_model        = D('Shop/ShopOrder');
		$this->cart_model         = D('Shop/ShopCart');
		$this->order_logic        = D('Shop/ShopOrder', 'Logic');
		$this->coupon_model       = D('Shop/ShopCoupon');
		$this->message_model      = D('Shop/ShopMessage');
		$this->user_coupon_model  = D('Shop/ShopUserCoupon');
		$this->coupon_logic       = D('Shop/ShopCoupon', 'Logic');
		$this->user_address_model = D('Shop/ShopUserAddress');
		$this->product_comment_model = D('Shop/ShopProductComment');
		$this->theme('mobile');
		$this->init_shop();
	}

	public function init_shop()
	{
		$configs = D('Config')
			->where(array('name' =>array('like', '_' . strtoupper(MODULE_NAME) . '_' . '%')))
			->limit(999)->select();
		$shop    = array();
		foreach ($configs as $k => $v)
		{
			$key                    = str_replace('_' . strtoupper(MODULE_NAME) . '_', '', strtoupper($v['name']));
			$shop[strtolower($key)] = $v['value'];
		}
		$sharedata = array(
			'title'=>$shop['title'],
			'desc'=>$shop['notice'],
//			'link'=>'',

			'imgUrl'=>'http://'.$_SERVER['HTTP_HOST'].pic($shop['logo']),
		);
		$this->mp_id = $shop['mp_id'];
		$this->init_wxjs();

		$this->assign('mp_id', $this->mp_id);
		$this->assign('shop', $shop);
		$this->assign('sharedata', $sharedata);

	}


	public function init_user()
	{
		$this->user_id = is_login();
		$this->init_wx();
		if (!$this->user_id)
		{
			$this->error('请在微信中打开');
//			if (IS_POST)
//			{
//				$this->error('请登录', U('shop/index/login'), 1);
//			}
//			else
//			{
//				redirect(U('shop/index/login'));
//			}

		}
		else if (!is_login())
		{
			$Menber_model = new \Admin\Model\MemberModel();
			$Menber_model->login($this->user_id);
		}
	}

	/*
	 * 微信登陆
	 */
	public function init_wx()
	{
		$isWeixinBrowser = isWeixinBrowser();
		if (!$isWeixinBrowser)
		{                           //非微信浏览器返回false，调用此函数必须对false结果进行判断，非微信浏览器不可访问调用的controller
			return false;
		}
		(get_mpid()==-1) && get_mpid($this->mp_id);
		$this->wx_id = get_ucuser_mid();   //获取粉丝用户mid，一个神奇的函数，没初始化过就初始化一个粉丝
		if ($this->wx_id === false)
		{
			//				$this->error('只可在微信中访问');
			return false;
		}

		$Ucuser = get_mid_ucuser($this->wx_id);
		//获取公众号信息

        //注册pc端帐号，
		if(!$Ucuser['uid'])
		{
			$Ucuser['uid'] = UCenterMember()->add(array('status'=>1,'update_time'=>$_SERVER['REQUEST_TIME']));
			D('Common/Member')->initUserRoleInfo(1,$Ucuser['uid']);
			D('UserRole')->add(array('uid'=>$Ucuser['uid'],'status'=>1,'role_id'=>1,'step'=>'finsh','init'=>1));
			D('Ucuser')->save($Ucuser);
		}
		$this->user_id = $Ucuser['uid'];
	}
	/*
	 * 初始化微信js
	 */
	public function init_wxjs()
	{
		$isWeixinBrowser = isWeixinBrowser();
		if (!$isWeixinBrowser)
		{                           //非微信浏览器返回false，调用此函数必须对false结果进行判断，非微信浏览器不可访问调用的controller
			return false;
		}
		$appinfo = get_mpid_appinfo($this->mp_id);
		$this->assign('appinfo', $appinfo);
		//初始化options信息
		$options['appid']          = $appinfo['appid'];
		$options['appsecret']      = $appinfo['secret'];
		$options['encodingaeskey'] = $appinfo['encodingaeskey'];
		$this->weObj               = new TPWechat($options);
		$this->weObj->checkAuth();
		$isWeixinBrowser = isWeixinBrowser();
		if (!$isWeixinBrowser)
		{                           //非微信浏览器返回false，调用此函数必须对false结果进行判断，非微信浏览器不可访问调用的controller
			return false;
		}
		$js_ticket = $this->weObj->getJsTicket();
		if (!$js_ticket)
		{
			$this->error('获取js_ticket失败！错误码：' . $this->weObj->errCode . ' 错误原因：' . ErrCode::getErrText($this->weObj->errCode));
		}
		$url     = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$js_sign = $this->weObj->getJsSign($url);
		$this->assign('js_sign', $js_sign);
		//默认的分享链接
		$surl = get_shareurl();
		if (!empty($surl))
		{
			$this->assign('share_url', $surl);
		}
	}


	public function login()
	{

		if (IS_POST)
		{
			$result = A('Ucenter/Login', 'Widget')->doLogin();
			if ($result['status'])
			{
				$this->success($result['info'], U('index/index'));
			}
			else
			{
				$this->error($result['info']);
			}
		}
		else
		{ //显示登录页面
			$this->display();
		}
	}




	public function index()
	{
		$option['page']   = I('page', '1', 'intval');
		$option['r']      = I('r', '10', 'intval');
		$option['cat_id'] = I('cat_id', '', 'intval');
		$option['cat_id'] = I('cat_id');
		if (empty($option['cat_id']))
		{
			unset($option['cat_id']);
		}
		$option['status'] = 0;//只要上架的商品
		$products         = $this->product_model->get_product_list($option);
		$slides         = M('shop_slides')->where('status = 0')->order('sort desc')->select();
		$this->assign('products', $products);
		$this->assign('slides', $slides);
		$this->display();

	}

	public function product()
	{
		$id      = I('id', '', 'intval');
		$product = $this->product_model->get_product_by_id($id);
		$this->assign('product', $product);
		$sharedata = array(
			'title'=>$product['title'],
//			'desc'=>,
			'imgUrl'=>'http://'.$_SERVER['HTTP_HOST'].pic($product['main_img']),
		);
		$this->assign('sharedata', $sharedata);
		$this->theme('mobile')->display();
	}

	public function cart()
	{
		$this->init_user();
		$cart = $this->cart_model->get_shop_cart_by_user_id($this->user_id);
		$this->assign('cart', $cart);
		$this->display();
	}


	public function makeorder()
	{
//		$_POST['products']    = '{"0":{"sku_id":"4","quantity":1}}';
//		$_REQUEST['pay_type'] = '11';
		$this->init_user();
		if (IS_POST)
		{
			//todo 也可以通过购物车id来取, 下单完成后可以清除下购物车

			if (isset($_REQUEST['products']) && !($products = I('post.products')) )
			{
				$this->error('商品参数错误');
			}
//             购物车 cart_id = array(1,2,3)
			if (isset($_REQUEST['cart_id']) && (!($cart_id = I('cart_id',''))
				|| !($GLOBALS['_TMP']['cart_id'] = $cart_id)
				|| !($products = $this->cart_model->get_shop_cart_by_ids($cart_id, $this->user_id)))
			)
			{
				$this->error('购物车数据有误');
			}

			foreach ($products as $k => $p)
			{
				if (!is_string($p['sku_id']) || !is_numeric($p['quantity']))
				{
					$this->error('参数错误');
				}
				$products[$k] = array(
					'sku_id'   => $products[$k]['sku_id'],
					'quantity' => $products[$k]['quantity']);
			}
			$order = array(
				'user_id'  => $this->user_id,
				'products' => $products,
			);

			//收货地址, 虚拟物品不要收货地址
			if (isset($_REQUEST['address_id']))
			{
				if (!($aid = I('address_id', false, 'intval')) ||
					!($address = $this->user_address_model->get_user_address_by_id($aid))
				)
				{
					$this->error('地址参数错误');
				}
			}
			else
			{
				isset($_REQUEST['name']) && $address['name'] = I('name','','text');
				isset($_REQUEST['phone']) && $address['phone']  = preg_match('/^([0-9\-\+]{3,16})$/',I('phone', '', 'text'),$ret)?'':$ret[0];
				isset($_REQUEST['province']) && $address['province'] = I('province','','text');
				isset($_REQUEST['city']) && $address['city'] = I('city','','text');
				isset($_REQUEST['town']) && $address['town'] = I('town','','text');
				//如果这里要5级分类,用冒号分开多级 如 ${town}:车公庙:金地花园:48栋301
				isset($_REQUEST['address']) && $address['address'] = I('address','','text');
			}
			//运送方式 express, ems, mail, self, virtual
			isset($_REQUEST['delivery']) && $address['delivery'] = I('delivery','','text');
			isset($address) && $order['address'] = $address;
//			//支付方式
//			if (!isset($_REQUEST['pay_type']) ||
//				!($order['pay_type'] = I('pay_type', ShopOrderModel::PAY_TYPE_WEIXINPAY,'intval'))
//			)
//			{
//				$this->error('选择支付方式');
//			}
			//使用优惠劵
			isset($_REQUEST['coupon_id']) && $order['coupon_id'] = I('coupon_id', '', 'intval');
			//留言 发票 提货时间 等其他信息
			$order['info'] = I('info', '', 'text');
			//增加下单后的钩子
			\Think\Hook::add('AfterMakeOrder', '\Shop\Logic\ShopOrderLogic');
			$ret           = $this->order_logic->make_order($order);
			if ($ret)
			{
				$this->success($ret);
			}
			else
			{
				$this->error('下单失败.' . $this->order_logic->error_str);
			}
		}
		else
		{
			//		$this->assign('su', $su);
			if(!($id = I('id','','intval'))
			 || !($product = $this->product_model->get_product_by_id($id)))
			{
				$product = array();
			}
			$quantity =I('quantity','1','intval');
			if(!($coupon_id = I('cookie.coupon_id','','intval'))
				|| !($coupon = $this->user_coupon_model->get_user_coupon_by_id($coupon_id)))
			{
				$coupon = array();
			}

			$sku = I('sku','','text');
			if( !empty($sku) && !($product['sku_table']['info'][$sku])){
				$product['price'] = $product['sku_table']['info'][$sku]['price'];
			}
			else {
				$sku = '';
			}

			$cart = $this->cart_model->get_shop_cart_by_user_id($this->user_id);
			$cart_id[0] = '';

			if (isset($_REQUEST['cart_id'])
				&& ( !($cart_id = I('cart_id','','text'))
					|| !(preg_match('/^\d+(,\d+)*$/',$cart_id))
					|| !($cart_id = explode(',',$cart_id))
					|| !($cart_list_products = $this->cart_model->get_shop_cart_by_ids($cart_id, $this->user_id)))
			) {
				redirect(U('shop/index/user'));
			}

			$address[0] = $this->user_address_model->get_last_user_address_by_user_id($this->user_id);

			$this->assign('quantity', $quantity);
			$this->assign('product', $product);
			$this->assign('coupon', $coupon);
			$this->assign('sku', $sku);
			$this->assign('cart_id', $cart_id);
			$this->assign('cart', $cart);
			$this->assign('cart_list_products', $cart_list_products);
			$this->assign('address',$address);
			$this->display();
		}
	}


	public function add_to_cart()
	{
		$this->init_user();
		if (!($shop_cart = $this->cart_model->create()))
		{
			$this->error($this->cart_model->getError());
		}
		$shop_cart['user_id'] = $this->user_id;
		$ret                  = $this->cart_model->add_shop_cart($shop_cart);
		if ($ret)
		{
			$this->success('成功');
		}
		else
		{
			$this->error('');
		}
	}


	public function delete_cart()
	{
		$this->init_user();
		$ids = I('ids', '');
		$ret = $this->cart_model->delete_shop_cart($ids, $this->user_id);
		if ($ret)
		{
			$this->success('成功');
		}
		else
		{
			$this->error();
		}
	}

	public function orders()
	{
		$this->init_user();
		$option['status'] = I('status',0,'intval');
		$option['page'] = I('1','','intval');
		$option['r'] = I('10','','intval');
		$option['user_id'] = $this->user_id;
		if(IS_POST)
		{
			$order_list = $this->order_model->get_order_list($option);
			$order_list['list'] = empty($order_list['list'])?array(): $order_list['list'];
			array_walk($order_list['list'],function(&$a)
			{
				empty($a['products']) ||
				array_walk($a['products'],function(&$b)
				{
					$b['main_img'] = (empty($b['main_img'])?'':pic($b['main_img']));
				});
			});
			$this->success($order_list);
		}
		ELSE
		{
			$this->assign('option', $option);
			$this->display();
		}


	}

	/*
	 * 获取用户的优惠券
	 */
	public function user_coupon()
	{
		$this->init_user();
		$type       = I('type', '', 'text');

		$option['page']    = I('page', '', 'intval');
		$option['r']       = I('r', '', 'intval');
		$option['user_id'] = $this->user_id;
		//可用的
		$option['available'] = I('available', 'true', 'bool');
		$GLOBALS['_TMP']['paid_fee'] = I('paid_fee','','intval'); //
		$user_coupons = $this->user_coupon_model->get_user_coupon_list($option);
		$this->assign('user_coupons', $user_coupons);
		$this->assign('type', $type);
		$this->display();
	}

	/*
	 * 可领优惠券列表
	 */
	public function coupons()
	{
		isset($_REQUEST['available']) && $option['available'] = I('available', 'true', 'bool');
		$coupon = $this->coupon_model->get_coupon_lsit($option);
		$this->assign('coupon', $coupon);
		//		var_dump(__file__.' line:'.__line__,$coupons);exit;
		$this->display();
	}

	/*
	 * 领取优惠券
	 */
	public function add_user_coupon()
	{
		$this->init_user();
		$coupon_id = I('coupon_id', '', 'intval');
		if (
			empty($coupon_id)
			|| !($coupon = $this->coupon_model->get_coupon_by_id($coupon_id))
		)
		{
			$this->error('优惠券不存在');//id 解密对不上
		}

		$ret = $this->coupon_logic->add_a_coupon_to_user($coupon['id'], $this->user_id);

		if ($ret)
		{
			$this->success('领取成功');
		}
		else
		{
			$this->error('领取失败，' . $this->coupon_logic->error_str);
		}
	}

	/*
	 * 商城建议
	 */
	public function suggest()
	{
		$this->init_user();
		if (IS_POST)
		{
			//提交处理
			$message = $this->message_model->create();
			if (!$message)
			{
				$this->error($this->message_model->getError());
			}
			$message['user_id'] = $this->user_id;
			$ret                = $this->message_model->add_or_edit_shop_message($message);
			if ($ret)
			{
				$this->success('提交成功。');
			}
			else
			{
				$this->error('提交失败。');
			}
		}
		else
		{
			$this->display();
		}
	}


	public function user()
	{
		$this->init_user();
		$su = query_user(array('avatar32', 'nickname', 'mobile'), $this->user_id);
//		$sql = 'select status,count(*) as count from uctoo_shop_order  where user_id = '.$this->user_id.' group by status';
//		$order_count_group_by_status = $this->order_model->query($sql);
		$order_count_group_by_status = $this->order_model->where('user_id = '.$this->user_id)->group('status')->getfield('status,count(1) as count');
		$this->assign('su', $su);
		$this->assign('order_count_group_by_status', $order_count_group_by_status);
		$this->display();
	}


	public function address()
	{
		$this->init_user();
		$option['page']    = I('page', '', 'intval');
		$option['r']       = I('r', -1, 'intval');
		$option['user_id'] = $this->user_id;
		$type = I('type','','text');
		$user_address_list = $this->user_address_model->get_user_address_list($option);
		$this->assign('address', $user_address_list);
		$this->assign('type', $type);
		$this->display();

	}

	public function addaddress()
	{
		$this->init_user();
		if (IS_POST)
		{
			$select = I('select','','intval');
			if($select && ($id =I('id','','intval') ))
			{
				empty($id) || $user_address = $this->user_address_model->get_user_address_by_id($id);
				$user_address = $this->user_address_model->create($user_address);
			}
			else
			{
				$user_address = $this->user_address_model->create();
			}
			if (!$user_address)
			{

				$this->error($this->user_address_model->getError());
			}
			$user_address['user_id'] = $this->user_id;
			$ret                     = $this->user_address_model->add_or_edit_user_address($user_address);
			if ($ret)
			{
				$this->success('操作成功。', U('shop/user_address'));
			}
			else
			{
				$this->error('操作失败。');
			}

		}
		else
		{
			$id = I('id','','intval');
			$address = $this->user_address_model->get_user_address_by_id($id);
			$this->assign('address', $address);
			$this->display();
		}
	}

	/*
	 * 取消订单
	 */
	public function cancel_order()
	{
		$this->init_user();
		if (IS_POST)
		{
			if (!($order_id = I('id', false, 'intval'))
				|| !($order = $this->order_model->get_order_by_id($order_id))
				|| !($order['user_id'] == $this->user_id)
			)
			{
				$this->error('参数错误');
			}
			$ret = $this->order_logic->cancal_order($order);
			if ($ret)
			{
				$this->success('成功取消订单');
			}
			else
			{
				$this->error('取消失败,' . $this->order_logic->error_str);
			}

		}
		else
		{
			$this->error('提交方式不合法');
		}
	}

	/*
	 * 确认收货
	 */
	public function do_receipt()
	{
		$this->init_user();
		if (IS_POST)
		{
			if (!($order_id = I('id', false, 'intval'))
				|| !($order = $this->order_model->get_order_by_id($order_id))
				|| !($order['user_id'] == $this->user_id)
			)
			{
				$this->error('参数错误');
			}
			$ret = $this->order_logic->recv_goods($order);
			if ($ret)
			{
				$this->success('操作成功');
			}
			else
			{
				$this->error('操作失败,' . $this->order_logic->error_str);
			}

		}
		else
		{
			$this->error('提交方式不合法');
		}
	}

	/*
	 * 订单评论
	 */
	public function comment()
	{
		$this->init_user();
		if(IS_POST)
		{
			$product_comments = I('product_comment');
			foreach($product_comments as &$product_comment)
			{
				$product_comment['user_id'] = $this->user_id;
				$product_comment['product_id'] = explode(';',$product_comment['product_id'])[0];
				if(!($product_comment =  $this->product_comment_model->create($product_comment)))
				{
					$this->error($this->product_comment_model->geterror());
				}


			}
			$ret = $this->order_logic->add_product_comment($product_comments);
			if(!$ret )
			{
				$this->error('评论失败，'.$this->order_logic->error_str);
			}
			if($ret )
			{
				$this->success('评论成功');
			}
		}
		else
		{
			$id = I('id','','intval');
			$order = $this->order_model->get_order_by_id($id);
			$this->assign('order', $order);
			$this->assign('products', $order['products']);
			$this->display();
		}

	}


	/*
	 * 订单详情
	 */
	public function orderdetail()
	{
		$id = I('id','','intval');
		$order = $this->order_model->get_order_by_id($id);
//		var_dump(__file__.' line:'.__line__,$order);exit;
		$this->assign('order', $order);
		$this->display();
	}

	public function test_pay($id='')
	{
		if(APP_DEBUG)
		{
			$order_model = D('Shop/ShopOrder');
			$order_logic = D('Shop/ShopOrder','Logic');
			$shop_order = $order_model->where('id ="'.$id.'"')->find();
			empty($shop_order) && $this->error('订单号错误');
			$shop_order['paid_time'] = time();
			$shop_order['pay_type'] = 9;
			$shop_order['pay_info'] =   array(
				'info' => 'this is test pay',
			);
			$shop_order['pay_info'] = json_encode($shop_order['pay_info']);
			$ret = $order_logic->pay_order($shop_order);//支付订单
			echo $ret?'成功':'失败,'.$order_logic->error_str;exit;
		}


	}

	public function commentlist()
	{
		$option['product_id']= I('product_id','','intval');
		$option['page'] = I('page','1','intval');
		if (IS_POST)
		{
//			$option['status'] = 1;//只取审核通过的
			$ret = $this->product_comment_model->get_product_comment_list($option);
			if($ret)
			{
				$this->success($ret);
			}
			else
			{
				$this->error();
			}

		}
		else{
			$this->assign('product_id', $option['product_id']);
			$this->display();
		}


	}


	/**
	 *
	 * jsApi微信支付示例
	 * 注意：
	 * 1、微信支付授权目录配置如下  http://test.uctoo.com/addon/Wxpay/Index/jsApiPay/mp_id/
	 * 2、支付页面地址需带mp_id参数
	 * 3、管理后台-基础设置-公众号管理，微信支付必须配置的参数都需填写正确
	 * @param array $mp_id 公众号在系统中的ID
	 * @return 将微信支付需要的参数写入支付页面，显示支付页面
	 *
	 *
	 *
	 *  参数 mp_id 微信公众号id
	 *      order_id 订单id
	 */

	public function jsApiPay(){
		$this->init_user();
		empty($this->mp_id) && $this->error('支付暂不可使用');//没配置收款公众号
		$info     = get_mpid_appinfo($this->mp_id);
		$mid = get_ucuser_mid();                         //获取粉丝用户mid，一个神奇的函数，没初始化过就初始化一个粉丝
//		if($mid === false){
//			$this->error('只可在微信中访问');
//		}
		$user = get_mid_ucuser($mid);                    //获取本地存储公众号粉丝用户信息
		$this->assign('user', $user);

		$surl = get_shareurl();
		if(!empty($surl)){
			$this->assign ( 'share_url', $surl );
		}

		$order_id = I('order_id',false,'intval');
		if(empty($order_id)) $this->error('缺少订单号'); //没订单号

		$odata = $this->order_logic->BeforePayOrder($order_id,$this->user_id,$this->mp_id);
		if(!$odata)
		{
			$this->error('订单初始化失败,'.$this->order_logic->error_str);
		}
		if (!($jsApiParameters = S('shop_order_' . $order_id . '_jsApiParameters')))
		{
			//获取公众号信息，jsApiPay初始化参数
			$cfg = array(
				'APPID'      => $info['appid'],
				'MCHID'      => $info['mchid'],
				'KEY'        => $info['mchkey'],
				'APPSECRET'  => $info['secret'],
				'NOTIFY_URL' => $info['notify_url'],
			);
			WxPayConfig::setConfig($cfg);

			//①、初始化JsApiPay
			$tools    = new JsApiPay();
			$wxpayapi = new WxPayApi();
			//检查订单状态 微信回调延迟或出错时 保证订单状态
			$inputs = new WxPayOrderQuery();
			$inputs->SetOut_trade_no($odata['order_id']);
			$result = $wxpayapi->orderQuery($inputs);
			if(array_key_exists("return_code", $result)
				&& array_key_exists("result_code", $result)
				&& array_key_exists("trade_state", $result)
				&& $result["return_code"] == "SUCCESS"
				&& $result["result_code"] == "SUCCESS"
				&& $result["trade_state"] == "SUCCESS"
				)
			{
				$this->order_logic->AfterPayOrder($result,$odata);
				redirect(U('shop/index/orderdetail',array('id'=>$order_id)));
			}
			//②、统一下单
			$input = new WxPayUnifiedOrder();           //这里带参数初始化了WxPayDataBase
			$input->SetBody($odata['product_name']);
			$input->SetAttach($odata['product_sku']);
			$input->SetOut_trade_no($odata['order_id']);
			$input->SetTotal_fee($odata['order_total_price']);
			$input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis", time() + 600));
			$input->SetTrade_type("JSAPI");
			$input->SetOpenid($user['openid']);

			$order = $wxpayapi->unifiedOrder($input);
			$jsApiParameters = $tools->GetJsApiParameters($order);
//			$editAddress = $tools->GetEditAddressParameters();
//			//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
			S('shop_order_' . $order_id . '_jsApiParameters', $jsApiParameters, 575);//设置缓存 缓存过期时间 稍微比微信支付过期短点
		}
		$this->assign ( 'order', $odata );
		$this->assign ( 'jsApiParameters', $jsApiParameters );
//		$this->assign ( 'editAddress', $editAddress );
		$this->display ();
	}

	/*
 * 取扫描支付二维码
 */
	public function nativepay()
	{
		$this->init_user();
		empty($this->mp_id) && $this->error('支付暂不可使用');//没配置收款公众号
		$info     = get_mpid_appinfo($this->mp_id);
		$order_id = I('order_id', '', 'intval');
		empty($order_id) && $this->error('缺少订单号');

		$odata = $this->order_logic->BeforePayOrder($order_id,$this->user_id,$this->mp_id);
		if(!$odata)
		{
			$this->error($this->order_logic->error_str);
		}

		if (!($result["code_url"] = S('shop_order_' . $order_id . '_code_url')))
		{
			//获取公众号信息，jsApiPay初始化参数
			$cfg = array(
				'APPID'      => $info['appid'],
				'MCHID'      => $info['mchid'],
				'KEY'        => $info['mchkey'],
				'APPSECRET'  => $info['secret'],
				'NOTIFY_URL' => $info['notify_url'],
			);
			WxPayConfig::setConfig($cfg);
			$notify = new NativePay();
			$input  = new WxPayUnifiedOrder();
			$input->SetBody($odata['product_name']);
			$input->SetOut_trade_no($odata['order_id']);
			$input->SetTotal_fee($odata['product_price']);
			$input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis", time() + 600));
			$input->SetTrade_type("NATIVE");
			$input->SetProduct_id($odata['product_id']);
			$result = $notify->GetPayUrl($input);
			S('shop_order_' . $order_id . '_code_url',$result["code_url"],575);
		}
		$this->assign ( 'order', $odata );
		$this->assign ( 'isWeixinBrowser', isWeixinBrowser() );
		$this->assign ( 'code_url', $result["code_url"] );
		$this->display ();
	}


	public function preview_delivery()
	{

//		var_dump(__file__.' line:'.__line__,$_REQUEST);exit;
		$address = array(
			'province' => I('province', '','text'),
			'city'     => I('city', '','text'),
			'town'     => I('town', '','text'),
		);

		$products = I('products','');
		if(empty($products))
		{
			$products = array(
				array(
					'id'   => I('id','','intval'), //商品id
					'count' => I('quantity', 1,'intval'), //商品数目
				));
		}
		else
		{
			is_array($products) || $this->error();
			foreach ($products as $k => &$p)
			{
				($p['id'] = I('data.id','','intval',$p)) || $this->error(1);
				($p['quantity'] = I('data.quantity','','intval',$p)) || $this->error(2);
				$products[$k]['count'] = $products[$k]['quantity'];
			}
		}
		$ret = $this->order_logic->precalc_delivery($products, $address);
		if($ret)
		{
			$this->success($ret);
		}
		else
		{
			$this->error();
		}

	}


	public function test()
	{
		$ret =D()->query('select * from a');
		$ret2 = array();
		foreach($ret as $l=>$a)
		{
			$ret2[$l]['id'] = $a['id'];
			$ret2[$l]['name'] = $a['name'];
			$ret2[$l]['type'] = $a['type'];
			$ret2[$l]['center'] =$a['4'].','.$a['5'];
		}
		$this->ajaxreturn($ret2);
		//		var_dump(__file__.' line:'.__line__,$ret2);exit;
	}



}
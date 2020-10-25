<?php

namespace app\pay\controller;

use \think\Db;
use \app\common\Pay;
use \app\common\PayAction;

class Mall extends Auth
{

	public $merchant = [];

	public $mch_user = [];

	public $card_no = [];

	public $open_user_id = null;

	public function __construct()
	{

		parent::__construct();

		$this->mch_user = [];
		$this->card_no = input('post.card_no/s');
		$this->open_user_id = model('MchUser')->get_uid(['buyer_id', 'mini_openid']);
		if(!empty($this->merchant['merchant_id'])) {
			if(!empty($this->card_no)) {
				$this->mch_user = model('MchUser')->get_user(['card_no' => $this->card_no, 'merchant_id' => $this->merchant['merchant_id']]);
			} else {
				$this->mch_user = model('MchUser')->get_user(['user_id|mini_openid' => $this->open_user_id, 'merchant_id' => $this->merchant['merchant_id']]);
			}
		}

	}

	public function load($class, $method = null)
	{
		if(empty($method)) {
			$method = $class;
		}
		$BefenClass = __CLASS__;
		$BefenClass .= '\\';
		$BefenClass .= $class;
		return $BefenClass::$method($this);
	}

	/**
	 * 商户交易号
	 */
	public function index($prefix = 'D')
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		if(!preg_match('/^[A-Z]{1,2}$/', $prefix)) {
			return make_json(0, '前缀格式错误');
		}
		$length = strlen($prefix);
		$out_trade_no = preg_replace('/\d{' . $length . '}$/', '', get_order_number($prefix));
		$contents = ['out_trade_no' => $out_trade_no];
		PayAction::log(JSON($contents));
		return make_json(1, 'ok', $contents);
	}

	/**
	 * 商品分类
	 */
	public function goods_cat()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$list = Db::name('goods_cat')->where([
			'merchant_id' => $this->merchant['merchant_id'],
			'is_show' => 1,
			'is_delete' => 0,
		])->field('cat_id, cat_name')->order('sort asc, cat_id desc')->select();
		return make_json(1, 'ok', ['list' => $list]);
	}

	/**
	 * 商品列表
	 * @param Int $cat_id 分类id
	 * @param String $goods_no 商品条形码
	 * @param String $keywords 商品关键词
	 */
	public function goods($cat_id = 0, $goods_no = '', $keywords = '')
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$where = [
			'merchant_id' => $this->merchant['merchant_id'],
			'status' => 1,
			'is_show' => 1,
			'is_delete' => 0,
		];
		if($cat_id) {
			$where['cat_id'] = $cat_id;
		}
		if(!empty($goods_no)) {
			$where['goods_no'] = $goods_no;
		}
		if(!empty($keywords)) {
			$where['goods_no|goods_name'] = ['like', '%' . $keywords . '%'];
		}
		$list = Db::name('goods')
			->where($where)
			->field('goods_id, cat_id, goods_name, price, unit, cover_pic, is_weigh, use_attr, goods_no, goods_stock, desc, content, hot_cakes')
			->order('sort asc, goods_id desc')
			->select();
		foreach ($list as $key => &$value) {
			$value['cover_pic'] = url('/', null, null, true) . preg_replace('/^\//', '', $value['cover_pic']);
		}
		unset($value);
		return make_json(1, 'ok', ['list' => $list]);
	}

	/**
	 * 商品详情
	 * @param String $goods_id 商品id
	 * @param String $goods_no 商品条形码
	 */
	public function goods_detail($goods_id = '', $goods_no = '')
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		if(empty($goods_id)) {
			$goods_id = input('post.id/s');
		}
		if(empty($goods_id)) {
			return make_json(0, '缺少参数 [goods_id]');
		}
		$goods = Db::name('goods')
			->alias('g')
			->join('goods_cat gc', 'gc.cat_id = g.cat_id', 'LEFT')
			->where(['g.merchant_id' => $this->merchant['merchant_id']])
			->where(['g.is_delete' => 0])
			->where(['goods_id|goods_no' => $goods_id])
			->field('g.goods_id, g.merchant_id, gc.cat_name, g.goods_name, g.price, g.cover_pic, g.unit, g.goods_stock, g.buy_limit, g.is_weigh, g.use_attr, g.attr')
			->find();
		if(empty($goods)) {
			return make_json(0, '商品未找到');
		}
		unset($goods['merchant_id']);
		$goods['cover_pic'] = url('/', null, null, true) . preg_replace('/^\//', '', $goods['cover_pic']);
		$goods['attr'] = json_decode($goods['attr'], true);
		foreach ($goods['attr'] as $key => &$value) {
			if(!empty($value['pic'])) {
				$value['pic'] = url('/', null, null, true) . preg_replace('/^\//', '', $value['pic']);
			}
		}
		unset($value);
		return make_json(1, 'ok', ['data' => $goods]);
	}

	/**
	 * 订单列表
	 * @param Int $page
	 * @param Int $pagenum
	 * @param String $status
	 * @param String $out_trade_no
	 */
	public function order($page = 1, $pagenum = 10, $status = '', $out_trade_no = '')
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$where = [];
		if((int)$this->person['manager']) {
			$where['o.store_id'] = ['=', $this->merchant['store_id']];
		} else {
			$where['o.person_id'] = ['=', $this->merchant['person_id']];
		}
		if(!empty($status)) {
			$where['o.status'] = $status;
		}
		if(!empty($out_trade_no)) {
			$where['o.out_trade_no'] = $out_trade_no;
		}
		$list = Db::name('order')
			->alias('o')
			->where($where)
			->field('o.order_id, o.time_create, o.trade_type, o.total_price, o.pay_price, o.out_trade_no, o.status')
			->order('o.order_id desc')
			->paginate($pagenum)
			->toArray();
		unset($value);
		return make_json(1, 'ok', [
			'list' => $list['data'],
			'total' => $list['total'],
			'pagenum' => $list['per_page'],
			'page' => $list['current_page'],
			'last_page' => $list['last_page'],
		]);
	}

	/**
	 * 订单详情
	 * @param String $order_id
	 */
	public function order_detail($order_id = '')
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		if(empty($order_id)) {
			return make_json(0, '缺少参数 [order_id]');
		}
		$order = Db::name('order')
			->where(['order_id' => $order_id])
			->where(['merchant_id' => $this->merchant['merchant_id']])
			->field('order_id, merchant_id, store_id, person_id, out_trade_no, time_create, trade_type, status, total_price, pay_price')
			->find();
		if(empty($order)) {
			return make_json(0, '订单不存在');
		}
		unset($order['merchant_id'], $order['store_id'], $order['person_id']);
		$order['goods'] = [];
		$goods = Db::name('order_detail')
			->alias('od')
			->join('goods g', 'od.goods_id = g.goods_id', 'LEFT')
			->where('od.order_id', '=', $order_id)
			->field('od.id, g.goods_name, od.price, od.num, g.cover_pic, od.is_refund, od.attr')
			->select();
		if(!empty($goods)) {
			foreach ($goods as $key => &$value) {
				$value['cover_pic'] = url('/', null, null, true) . preg_replace('/^\//', '', $value['cover_pic']);
				$value['attr'] = json_decode($value['attr'], true);
			}
			unset($value);
			$order['order_detail'] = $goods;
		}
		return make_json(1, 'ok', ['data' => $order]);
	}

	public function pay()
	{
		//return $this->pay_order();
	}

	/**
	 * 支付订单
	 * @param String $trade_type 必填 支付方式 face_code刷脸，bar_code扫码，cash现金，card余额，online线上支付
	 * @param String $biz_type 必填 业务类型 normal|charge
	 * @param String $out_trade_no 必填 商户交易号
	 * @param String $auth_code 可选 支付授权码
	 * @param Float $pay_price 必填 实付金额，单位元
	 * @param Float $total_price 必填 应付金额，单位元
	 * @param String $goods 必填 商品明细
	 * @param String $remark 可选 备注
	 * @param String $openid 可选 微信用户openid，会员充值、余额支付必传
	 * @param String $buyer_id 可选 支付宝用户user_id，会员充值、余额支付必传
	 */
	public function pay_order($trade_type = '', $biz_type = '', $out_trade_no = '', $auth_code = '', $pay_price = 0, $total_price = 0, $goods = '', $remark = '', $openid = '', $buyer_id = '')
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$trade_type = $trade_type ? $trade_type : input('post.trade_type/s');
		$biz_type = $biz_type ? $biz_type : input('post.biz_type/s');
		if(empty($biz_type)) {
			$biz_type = 'normal';
		}
		$out_trade_no = $out_trade_no ? $out_trade_no : input('post.out_trade_no/s');
		$auth_code = $auth_code ? $auth_code : input('post.auth_code/s');
		$pay_price = $pay_price ? $pay_price : input('post.pay_price/f', 0);
		$total_price = $total_price ? $total_price : input('post.total_price/f', 0);
		$goods = $goods ? $goods : input('post.goods/s');
		$remark = $remark ? $remark : input('post.remark/s');
		$openid = $openid ? $openid : input('post.openid/s');
		$buyer_id = $buyer_id ? $buyer_id : input('post.buyer_id/s');
		if(empty($trade_type)) {
			return make_json(0, '缺少参数 [trade_type]');
		}
		if(empty($biz_type)) {
			return make_json(0, '缺少参数 [biz_type]');
		}
		if(empty($out_trade_no)) {
			return make_json(0, '缺少参数 [out_trade_no]');
		}
		if(empty($pay_price)) {
			return make_json(0, '缺少参数 [pay_price]');
		}
		if(empty($total_price)) {
			return make_json(0, '缺少参数 [total_price]');
		}
		if(empty($goods)) {
			return make_json(0, '缺少参数 [goods]');
		}
		if(!in_array($trade_type, ['face_code', 'bar_code', 'cash', 'card'])) {
			return make_json(0, '非法参数 [trade_type]');
		}
		if(!in_array($biz_type, ['normal', 'charge'])) {
			return make_json(0, '非法参数 [biz_type]');
		}
		if(!is_array($goods)) {
			$goods = json_decode($goods, true);
		}
		if(empty($goods)) {
			return make_json(0, '非法参数 [goods]');
		}
		if(in_array($trade_type, ['face_code', 'bar_code'])) {
			$pay_client = Pay::client($auth_code);
			if(!$pay_client) {
				return make_json(0, '无法识别付款码');
			}
		}
		if($pay_price > $total_price) {
			return make_json(0, '支付金额错误');
		}
		if(0 != Db::name('trade')->where('out_trade_no', '=', $out_trade_no)->count()) {
			return make_json(0, '商户订单号已存在！');
		}
		$order = $this->order_create($trade_type, $out_trade_no, $pay_price, $total_price, $goods, $remark);
		$order = json_decode($order, true);
		if($order['status'] == 0) {
			return JSON($order);
		}
		$order_id = $order['contents']['order_id'];
		$order_detail = $order['contents']['order_detail'];
		switch($trade_type) {
			//现金
			case 'cash':
				Db::name('order')->where('order_id', '=', $order_id)->update([
					'status' => 3,
					'time_update' => _time(),
				]);
				Db::name('order_detail')->where('order_id', '=', $order_id)->update([
					'time_update' => _time(),
				]);
				return make_json(1, 'ok', ['data' => [
					'order_id' => $order_id,
					'order_detail' => $order_detail,
				]]);
			break;
			//会员卡
			case 'card':
				if(empty($this->mch_user)) {
					return make_json(0, '无法识别会员');
				}
				$res = make_return('0', 'ok', []);
				if($this->mch_user['balance'] < $pay_price) {
					$res['message'] = '会员卡余额不足';
				} else {
					$res['status'] = '1';
					$res['message'] = 'card';
					$this->mch_user = model('MchUser')->payment($this->merchant, $this->mch_user, $pay_price, $out_trade_no);
				}
				Db::name('order')->where('order_id', '=', $order_id)->update([
					'status' => 3,
					'time_update' => _time(),
				]);
				Db::name('order_detail')->where('order_id', '=', $order_id)->update([
					'time_update' => _time(),
				]);
				$res['contents']['data'] = ToString([
					'order_id' => $order_id,
					'order_detail' => $order_detail,
				]);
				$res['contents']['card_info'] = ToString($this->mch_user);
				return JSON($res);
			break;
			//线下支付
			default:
				$this->errMsg = Index::check_merchant($pay_client, $this->merchant);
				if($this->errMsg) {
					return make_json(0, $this->errMsg);
				}
				model('Trade')->_insert($this->merchant, $pay_client, $trade_type, $pay_price, $out_trade_no);
				$PostData = [
					'out_trade_no' => $out_trade_no,
					'auth_code' => $auth_code,
					'total_amount' => $pay_price,
					'subject' => $this->merchant['merchant_name'],
					'body' => $this->merchant['merchant_name'],
					'spbill_create_ip' => get_ipaddr(),
				];
				$res = PayAction::gate($pay_client)->merchant($this->merchant)->pay($PostData);
				$res = PayAction::result_filter($res);
				if($res->status == 0) {
					if((isset($res->contents->code) && $res->contents->code == 10003) || (isset($res->contents->err_code) && $res->contents->err_code == 'USERPAYING')) {
						return make_json(1, 'query', $res->contents);
					} else {
						return make_json(0, $res->message, $res->contents);
					}
				}
				$res->contents->order_id = $order_id;
				$res->contents->order_detail = $order_detail;
				return make_json(1, 'query', $res->contents);
			break;
		}
	}

	/**
	 * 商品订单
	 * @param String $trade_type 必填 支付方式 face_code刷脸，bar_code扫码，cash现金，card余额
	 * @param String $out_trade_no 可选 交易号
	 * @param String $pay_price 必填 实付金额，单位元
	 * @param String $total_price 必填 应付金额，单位元
	 * @param String $goods 必填 商品明细
	 * @param String $remark 可选 备注
	 */
	public function order_create($trade_type = '', $out_trade_no = '', $pay_price = 0, $total_price = 0, $goods = '', $remark = '')
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$trade_type = $trade_type ? $trade_type : input('post.trade_type/s');
		$out_trade_no = $out_trade_no ? $out_trade_no : input('post.out_trade_no/s');
		$pay_price = $pay_price ? $pay_price : input('post.pay_price/f', 0);
		$total_price = $total_price ? $total_price : input('post.total_price/f', 0);
		$goods = $goods ? $goods : input('post.goods/s');
		$remark = $remark ? $remark : input('post.remark/s', '');
		if(empty($trade_type)) {
			return make_json(0, '缺少参数 [trade_type]');
		}
		if(!in_array($trade_type, ['face_code', 'bar_code', 'cash', 'card'])) {
			return make_json(0, '非法参数 [trade_type]');
		}
		if(empty($out_trade_no)) {
			return make_json(0, '缺少参数 [out_trade_no]');
		}
		if(empty($pay_price)) {
			return make_json(0, '缺少参数 [pay_price]');
		}
		if(empty($total_price)) {
			return make_json(0, '缺少参数 [total_price]');
		}
		if(!is_array($goods)) {
			$goods = json_decode($goods, true);
		}
		if(empty($goods)) {
			return make_json(0, '缺少参数 [goods]');
		}
		if(0 != Db::name('order')->where('out_trade_no', '=', $out_trade_no)->count()) {
			return make_json(0, '订单号已存在，请更换订单号再发起！');
		}
		//判断库存
		$low_stock = [];
		foreach($goods as $key => $val) {
			$goods_field = Db::name('goods')->where('goods_id', '=', $val['goods_id'])->field('goods_name, goods_stock')->find();
			if($goods_field['goods_stock'] != -1 && $goods_field['goods_stock'] < $val['num']) {
				$low_stock[$val['goods_id']] = [
					'goods_name' => $goods_field['goods_name'],
					'goods_stock' => $goods_field['goods_stock'],
				];
			}
		}
		if($low_stock) {
			return make_json(0, '商品库存不足', [
				'list' => $low_stock
			]);
		}
		//写入订单
		$order_id = Db::name('order')->insertGetId([
			'merchant_id' => $this->merchant['merchant_id'],
			'store_id' => $this->merchant['store_id'],
			'device_id' => $this->merchant['device_id'],
			'person_id' => $this->merchant['person_id'],
			'trade_type' => $trade_type,
			'out_trade_no' => $out_trade_no,
			'pay_price' => $pay_price,
			'total_price' => $total_price,
			'status' => 0,
			'remark' => $remark,
			'time_create' => _time()
		]);
		//写入订单详情
		$detail = [];
		foreach ($goods as $k => $g) {
			$g['attr'] = empty($g['attr']) ? [] : $g['attr'];
			$detail[] = [
				'order_id' => $order_id,
				'goods_id' => $g['goods_id'],
				'attr' => JSON($g['attr']),
				'num' => $g['num'],
				'price' => $g['price'],
				'pic' => model('Goods')->getPic($g['goods_id'], $g['attr']),
				'time_create' => _time()
			];
			//库存处理
			Db::name('goods')->where('goods_id', '=', $g['goods_id'])->where('goods_stock', '<>', '-1')->setDec('goods_stock', $g['num']);
		}
		Db::name('order_detail')->insertAll($detail);
		$order_detail = Db::name('order_detail')->where('order_id', '=', $order_id)->select();
		return make_json(1, 'ok', [
			'order_id' => $order_id,
			'order_detail' => $order_detail
		]);
	}

	/**
	 * 查询接口
	 * @param String $out_trade_no
	 */
	public function query()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return JSON(Index::query($this->merchant, input('post.out_trade_no/s')));
	}

	/**
	 * 退款接口
	 * @param String $out_trade_no 必填 商户交易号
	 * @param String $out_refund_no 可选 退款交易号
	 * @param Float $refund_amount 可选 退款金额
	 * @param String $refund_reason 可选 退款原因
	 * @param Int $order_id 可选 订单id
	 * @param Int $order_detail_id 可选 订单详情id
	 */
	public function refund()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return JSON(Index::refund($this->merchant, input('post.out_trade_no/s'), [
			'out_refund_no' => input('post.out_refund_no/s'),
			'refund_amount' => input('post.refund_amount/f', 0),
			'refund_reason' => input('post.refund_reason/s'),
			'order_id' => input('post.order_id/d', 0),
			'order_detail_id' => input('post.order_detail_id/d', 0),
		]));
	}

	/**
	 * 退款查询接口
	 * @param String $out_trade_no
	 * @param String $out_refund_no
	 */
	public function query_refund()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return JSON(Index::query_refund($this->merchant, input('post.out_trade_no/s'), input('post.out_refund_no/s')));
	}

	/**
	 * 广告接口(首页)
	 * @param String $SN
	 */
	public function slider()
	{
		$item = [url('/', null, null, true) . 'uploads/test/d2_index.jpg?t=' . gsdate('Y-m-d-H')];
		return make_json(1, 'ok', [
			'time' => 5,
			'item' => $item
		]);
	}

	/**
	 * 广告接口(内页)
	 * @param String $SN
	 */
	public function slider_result()
	{
		$value = Db::name('store_device')->where('status', '=', '1')->where('SN', '=', input('post.SN/s'))->find();
		$ads = json_decode($value['ads']);
		if(!empty($ads->item)) {
			return make_json(1, 'ok', $ads);
		} else {
			$item = [url('/', null, null, true) . 'uploads/test/d2_result.jpg?t=' . gsdate('Y-m-d-H')];
			return make_json(1, 'ok', [
				'time' => 5,
				'item' => $item
			]);
		}
	}

}


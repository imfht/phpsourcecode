<?php

namespace app\mo\controller;

use \think\Db;
use \app\common\Upload;

class Merchant
{

	public $agent;

	public function __construct()
	{
		$this->agent = model('Agent')->checkLoginAgent();
	}

	public function index()
	{
		$where = [];
		if(input('param.wd')) {
			$where['m.merchant_no|m.merchant_name'] = ['LIKE', '%' . input('param.wd') . '%'];
		}
		if(input('param.merchant_id')) {
			$where['m.merchant_id'] = ['=', input('param.merchant_id')];
		} else {
			if(input('param.merchant_name')) {
				$where['m.merchant_name'] = ['LIKE', '%' . input('param.merchant_name') . '%'];
			}
		}
		$where['m.check_status'] = ['=', '-1'];

		if(request()->isPost()) {
			$object = Db::name('merchant')
				->alias('m')
				->join('agent a', 'm.agent_id = a.agent_id', 'LEFT')
				->where($where)
				->where('m.agent_id', '=', $this->agent['agent_id'])
				->order('m.merchant_id', 'DESC')
				->field('m.*, a.agent_no')
				->paginate(20, false, ['query' => request()->param()])
				->each(function($item, $key) {
					$item['status'] = model('merchant')::getStatus($item['status']);
					$item['time_create'] = gsdate('Y-m-d', $item['time_create']);
					$item['trade_rates'] = number_format($item['trade_rates'], 2);
					return $item;
				});
			$array = $object->toArray();
			$total = $array['total'];
			$list = $array['data'];
			$per_page = $array['per_page'];
			$current_page = $array['current_page'];
			$last_page = $array['last_page'];
		
			$data = [
				'list' => $list,
				'total' => $total,
				'last_page' => $last_page
			];
			return make_json(1, 'ok', $data);
		}
		include \befen\view();
	}

	public function check()
	{
		$where = [];
		if(input('param.wd')) {
			$where['m.merchant_no|m.merchant_name'] = ['LIKE', '%' . input('param.wd') . '%'];
		}
		//$where['m.check_status'] = ['<>', '-1'];
		if(request()->isPost()) {
			$object = Db::name('merchant')
				->alias('m')
				->join('agent a', 'm.agent_id = a.agent_id', 'LEFT')
				->where($where)
				->where('m.agent_id', '=', $this->agent['agent_id'])
				->order('m.merchant_id', 'DESC')
				->field('m.*, a.agent_no')
				->paginate(20, false, ['query' => request()->param()])
				->each(function($item, $key) {
					$item['check_status_text'] = model('merchant')::getCheckStatus($item['check_status']);
					return $item;
				});
			$array = $object->toArray();
			$total = $array['total'];
			$list = $array['data'];
			$per_page = $array['per_page'];
			$current_page = $array['current_page'];
			$last_page = $array['last_page'];
			$data = [
				'list' => $list,
				'total' => $total,
				'last_page' => $last_page
			];
			return make_json(1, 'ok', $data);
		}
		include \befen\view();
	}

	public function add()
	{
		$request = request();
		if($request->isPost()) {
			$post = input('post.');
			$post['time_create'] = _time();
			if(empty(input('param.merchant_name/s'))) {
				return make_json(0, '商户名称必填');
			}
			if(Db::name('merchant')->where('per_phone', '=', $post['per_phone'])->count()) {
				return make_json(0, '负责人电话已经存在');
			}
			$post['agent_id'] = $this->agent['agent_id'];
			$password = get_rand(12);
			$post['password'] = authcode($password, 'ENCODE');

			$id_card_copy = $request->file('id_card_copy');
			if($id_card_copy) {
				$id_card_copy = Upload::index('id_card_copy');
				if($id_card_copy['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['id_card_copy'] = $id_card_copy['message'];
			} else {
				return make_json(0, '身份证正面必填');
			}
			$id_card_national = $request->file('id_card_national');
			if($id_card_national) {
				$id_card_national = Upload::index('id_card_national');
				if($id_card_national['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['id_card_national'] = $id_card_national['message'];
			} else {
				return make_json(0, '身份证反面必填');
			}
			$license_copy = $request->file('license_copy');
			if($license_copy) {
				$license_copy = Upload::index('license_copy');
				if($license_copy['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['license_copy'] = $license_copy['message'];
			} else {
				return make_json(0, '营业执照必填');
			}
			$store_door = $request->file('store_door');
			if($store_door) {
				$store_door = Upload::index('store_door');
				if($store_door['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['store_door'] = $store_door['message'];
			} else {
				return make_json(0, '门店门口照片必填');
			}
			$store_inside = $request->file('store_inside');
			if($store_inside) {
				$store_inside = Upload::index('store_inside');
				if($store_inside['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['store_inside'] = $store_inside['message'];
			} else {
				return make_json(0, '店内环境照片必填');
			}
			$qualifications = $request->file('qualifications');
			if($qualifications) {
				$qualifications = Upload::index('qualifications');
				if($qualifications['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['qualifications'] = $qualifications['message'];
			}
			$license_auth = $request->file('license_auth');
			if($license_auth) {
				$license_auth = Upload::index('license_auth');
				if($license_auth['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['license_auth'] = $license_auth['message'];
			}

			model('Merchant')->allowField(true)->save($post);
			$merchant_id = model('Merchant')->getLastInsID();
			$merchant_no = $merchant_id + pow(10, 9);
			model('Merchant')->allowField(true)->save(['merchant_no' => $merchant_no], ['merchant_id' => $merchant_id]);
			$post['account_id'] = $merchant_id;
			model('Account')->allowField(true)->save($post);
			Db::name('merchant_weixin')->insert(['merchant_id' => $merchant_id]);
			Db::name('merchant_alipay')->insert(['merchant_id' => $merchant_id]);
			/* HjSync */
			//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@merchant', ['merchant_id' => $merchant_id]);
			/* HjSync */
			return make_json(1, '添加商户成功');
		}
		include \befen\view();
	}

	public function detail($merchant_id)
	{
		$value = Db::name('merchant')
			->alias('m')
			->join('account a', 'm.merchant_id = a.account_id', 'LEFT')
			->where('m.agent_id', '=', $this->agent['agent_id'])
			->where('m.merchant_id', '=', $merchant_id)
			->field('m.*, a.*')
			->find();
		if(request()->isPost()) {
			$request = request();
			$post = input('post.');
			$post['time_update'] = _time();
			if(empty(input('param.merchant_name/s'))) {
				return make_json(0, '商户名称必填');
			}
			if(Db::name('merchant')->where('per_phone', '=', $post['per_phone'])->where('merchant_id', '<>', $merchant_id)->count()) {
				return make_json(0, '负责人电话已经存在');
			}
			$id_card_copy = $request->file('id_card_copy');
			if($id_card_copy) {
				$id_card_copy = Upload::index('id_card_copy');
				if($id_card_copy['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['id_card_copy'] = $id_card_copy['message'];
			}
			$id_card_national = $request->file('id_card_national');
			if($id_card_national) {
				$id_card_national = Upload::index('id_card_national');
				if($id_card_national['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['id_card_national'] = $id_card_national['message'];
			}
			$license_copy = $request->file('license_copy');
			if($license_copy) {
				$license_copy = Upload::index('license_copy');
				if($license_copy['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['license_copy'] = $license_copy['message'];
			}
			$store_door = $request->file('store_door');
			if($store_door) {
				$store_door = Upload::index('store_door');
				if($store_door['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['store_door'] = $store_door['message'];
			}
			$store_inside = $request->file('store_inside');
			if($store_inside) {
				$store_inside = Upload::index('store_inside');
				if($store_inside['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['store_inside'] = $store_inside['message'];
			}
			$qualifications = $request->file('qualifications');
			if($qualifications) {
				$qualifications = Upload::index('qualifications');
				if($qualifications['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['qualifications'] = $qualifications['message'];
			}
			$license_auth = $request->file('license_auth');
			if($license_auth) {
				$license_auth = Upload::index('license_auth');
				if($license_auth['status'] != 1) {
					return make_json(0, '图片上传失败');
				}
				$post['license_auth'] = $license_auth['message'];
			}

			model('Account')->allowField(true)->save($post, ['account_id' => $merchant_id]);
			if(input('post.submit2')) {
				model('Merchant')->allowField(true)->save($post, ['merchant_id' => $merchant_id]);
				/* HjSync */
				//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@merchant', ['merchant_id' => $merchant_id]);
				/* HjSync */
				return make_json(1, '保存成功');
			}
			if(input('post.submit3')) {
				$post['check_status'] = 1;
				model('Merchant')->allowField(true)->save($post, ['merchant_id' => $merchant_id]);
				/* HjSync */
				//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@merchant', ['merchant_id' => $merchant_id]);
				/* HjSync */
				return make_json(1, '操作成功');
			}
		}
		include \befen\view();
	}

	public function passwd($merchant_id)
	{
		if(request()->isPost()) {
			$post = [];
			$post['password'] = authcode(input('post.password'), 'ENCODE');
			model('Merchant')->allowField(true)->save($post, ['merchant_id' => $merchant_id]);
			model('Config')->config('sms_switch') && \think\Queue::push('\app\pay\job\AliSend@reset_password', ['merchant_id' => $merchant_id]);
			/* HjSync */
			//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@merchant', ['merchant_id' => $merchant_id]);
			/* HjSync */
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function natural($merchant_id)
	{
		$value = Db::name('merchant m')
			->join('account a', 'm.merchant_id = a.account_id', 'LEFT')
			->where('m.agent_id', '=', $this->agent['agent_id'])
			->where('m.merchant_id', '=', $merchant_id)
			->field('m.*, a.*')
			->find();
		include \befen\view();
	}

	public function upload($merchant_id)
	{
		return \app\common\Upload::index();
	}

	public function get_ext($filename) {
		$temp_array = explode(".", trim($filename));
		if(count($temp_array) < 2) {
			$ext = '';
		} else {
			$ext = array_pop($temp_array);
			$ext = trim($ext);
			$ext = strtolower($ext);
		}
		return $ext;
	}

	public function device($merchant_id)
	{
		$value = Db::name('store_device')
			->alias('sd')
			->join('agent a', 'a.agent_id = sd.agent_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = sd.merchant_id', 'LEFT')
			->where('m.agent_id', '=', $this->agent['agent_id'])
			->where('m.merchant_id', '=', $merchant_id)
			->field('sd.*, a.agent_name, m.merchant_id, m.merchant_no')
			->find();
		include \befen\view();
	}

	public function get_url_auth_token($merchant_id)
	{
		//商家授权URL
		if(0 == Db::name('merchant')->where('agent_id', '=', $this->agent['agent_id'])->where('merchant_id', '=', $merchant_id)->count()) {
			echo url('/', null, null, true);
		} else {
			$alipay = new \app\pay\controller\Alipay();
			echo $alipay->get_url_auth_token(authcode($merchant_id, 'ENCODE', '', 300));
		}
	}

}


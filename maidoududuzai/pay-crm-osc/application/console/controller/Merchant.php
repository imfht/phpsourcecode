<?php

namespace app\console\controller;

use \think\Db;

class Merchant
{

	public $admin;

	public function __construct()
	{
		$this->admin = model('Admin')->checkLoginAdmin();
	}

	public function index()
	{
		$where = [];
		if(input('param.wd')) {
			$where['m.merchant_no|m.merchant_name'] = ['LIKE', '%'.input('param.wd').'%'];
		}
		if(input('param.agent_id')) {
			$where['m.agent_id'] = ['=', input('param.agent_id')];
		} else {
			if(input('param.agent_name')) {
				$where['a.agent_no|a.agent_name'] = ['LIKE', '%'.input('param.agent_name').'%'];
			}
		}
		if(input('param.merchant_id')) {
			$where['m.merchant_id'] = ['=', input('param.merchant_id')];
		} else {
			if(input('param.merchant_name')) {
				$where['m.merchant_id|m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
			}
		}
		$where['m.check_status'] = ['=', '-1'];
		$object = Db::name('merchant')
			->alias('m')
			->join('agent a', 'm.agent_id = a.agent_id', 'LEFT')
			->join('wx_user wx', 'm.openid = wx.openid', 'LEFT')
			->where($where)
			->order('m.merchant_id', 'DESC')
			->field('m.*, a.agent_no, a.agent_name, wx.nickname, wx.headimgurl')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
		if(request()->isPost()) {
			$data = [
				'list' => $list,
				'total' => $total,
			];
			return make_json(1, 'ok', $data);
		}
		if(input('param.action') == 'export') {
			$where = [];
			if(input('param.agent_id')) {
				$where['a.agent_id'] = ['=', input('param.agent_id')];
			} else {
				if(input('param.agent_name')) {
					$where['a.agent_no|a.agent_name'] = ['LIKE', '%'.input('param.agent_name').'%'];
				}
			}
			$list = Db::name('merchant')
				->alias('m')
				->join('agent a', 'a.agent_id = m.agent_id', 'LEFT')
				->where($where)
				->field('a.agent_no, a.agent_name, m.merchant_no, m.merchant_name, m.merchant_industry, m.per_name, m.per_phone, m.province, m.city, m.county, m.address, m.time_create')
				->order('m.merchant_id', 'ASC')
				->select();
			$data = [];
			$i = 0;
			$i++;
			$data[$i] = ['序号', '商户名称', '商户编号', '代理商名称', '代理商编号', '商户类型', '负责人姓名', '负责人电话', '商户地区', '商户地址', '创建时间'];
			foreach($list as $val) {
				$i++;
				$data[$i] = [$i - 1, $val['merchant_name'], $val['merchant_no'], $val['agent_name'], $val['agent_no'], $val['merchant_industry'], $val['per_name'], $val['per_phone'], $val['province'] . $val['city'] . $val['county'], $val['address'], gsdate('Y-m-d H:i:s', $val['time_create'])];
			}
			$_Key = [];
			foreach(range('A', 'Z') as $val) {
				$_Key[] = $val;
			}
			require_once EXTEND_PATH . 'PHPExcel/PHPExcel.php';
			require_once EXTEND_PATH . 'PHPExcel/PHPExcel/IOFactory.php';
			$Excel = new \PHPExcel();
			foreach($data as $key => $val) {
				if($key == 0) {
					continue;
				}
				$i = 0;
				foreach($val as $value) {
					$Excel->setActiveSheetIndex(0)->setCellValueExplicit($_Key[$i] . $key, (string)$value, 's');
					if($key == 1) {
						$Excel->getActiveSheet()->getStyle($_Key[$i] . $key)->getFont()->setBold(true);
					}
					$i++;
				}
			}
			$Excel->getActiveSheet()->getColumnDimension('A')->setWidth(10); //序号
			$Excel->getActiveSheet()->getColumnDimension('B')->setWidth(30); //商户名称
			$Excel->getActiveSheet()->getColumnDimension('C')->setWidth(15); //商户编号
			$Excel->getActiveSheet()->getColumnDimension('D')->setWidth(30); //代理商名称
			$Excel->getActiveSheet()->getColumnDimension('E')->setWidth(15); //代理商编号
			$Excel->getActiveSheet()->getColumnDimension('F')->setWidth(15); //商户类型
			$Excel->getActiveSheet()->getColumnDimension('G')->setWidth(15); //负责人姓名
			$Excel->getActiveSheet()->getColumnDimension('H')->setWidth(15); //负责人电话
			$Excel->getActiveSheet()->getColumnDimension('I')->setWidth(30); //商户地区
			$Excel->getActiveSheet()->getColumnDimension('J')->setWidth(30); //商户地址
			$Excel->getActiveSheet()->getColumnDimension('K')->setWidth(20); //创建时间
			header("Cache-Control: no-cache, must-revalidate");
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=商户列表_" . gsdate('Y-m-d') . ".xls");
			$Writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
			$Writer->save('php://output');
			exit();
		}
		include \befen\view();
	}

	public function check()
	{
		$where = [];
		if(input('param.wd')) {
			$where['m.merchant_no|m.merchant_name'] = ['LIKE', '%'.input('param.wd').'%'];
		}
		if(input('param.agent_id')) {
			$where['m.agent_id'] = ['=', input('param.agent_id')];
		} else {
			if(input('param.agent_name')) {
				$where['a.agent_no|a.agent_name'] = ['LIKE', '%'.input('param.agent_name').'%'];
			}
		}
		if(input('param.merchant_id')) {
			$where['m.merchant_id'] = ['=', input('param.merchant_id')];
		} else {
			if(input('param.merchant_name')) {
				$where['m.merchant_no|m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
			}
		}
		//$where['m.check_status'] = ['<>', '-1'];
		$object = Db::name('merchant')
			->alias('m')
			->join('agent a', 'm.agent_id = a.agent_id', 'LEFT')
			->where($where)
			->order('m.merchant_id', 'DESC')
			->field('m.*, a.agent_no, a.agent_name')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		if(request()->isPost()) {
			$data = [
				'list' => $list,
				'total' => $total,
			];
			return make_json(1, 'ok', $data);
		}
		$pagenav = $object->render();
		include \befen\view();
	}

	public function get_id_authcode($merchant_id)
	{
		return make_json(1, 'ok', ['merchant_id' => authcode($merchant_id, 'ENCODE', '', 60)]);
	}

	public function get_url_auth_token($merchant_id)
	{
		// 商家授权URL
		if(0 == Db::name('merchant')->where('merchant_id', '=', $merchant_id)->count()) {
			echo url('/', null, null, true);
		} else {
			$alipay = new \app\pay\controller\Alipay();
			echo $alipay->get_url_auth_token(authcode($merchant_id, 'ENCODE', '', 300));
		}
	}

	public function add()
	{
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_create'] = _time();
			if(Db::name('merchant')->where('per_phone', '=', $post['per_phone'])->count()) {
				return make_json(0, '负责人电话已经存在');
			}
			//$post['agent_id'] = 0;
			$password = get_rand(12);
			$post['password'] = authcode($password, 'ENCODE');
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
			\think\Queue::push('\app\pay\job\AliSend@add_merchant', ['merchant_id' => $merchant_id]);
			return make_json(1, '添加商户成功', [
				'merchant_id' => $merchant_id,
				'merchant_no' => $merchant_no,
				'merchant_password' => $password,
			]);
		}
		include \befen\view();
	}

	public function detail($merchant_id)
	{
		$value = Db::name('merchant')
			->alias('m')
			->join('account a', 'a.account_id = m.merchant_id', 'LEFT')
			->where('m.merchant_id', '=', $merchant_id)
			->field('m.*, a.*')
			->find();
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_update'] = _time();
			if(Db::name('merchant')->where('per_phone', '=', $post['per_phone'])->where('merchant_id', '<>', $merchant_id)->count()) {
				return make_json(0, '负责人电话已经存在');
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
				$post['check_status'] = -1;
				model('Merchant')->allowField(true)->save($post, ['merchant_id' => $merchant_id]);
				model('Config')->config('mail_switch') && \think\Queue::push('\app\pay\job\AliSend@audit_merchant', ['agent_id' => $value['agent_id'], 'merchant_id' => $value['merchant_id']]);
				/* HjSync */
				//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@merchant', ['merchant_id' => $merchant_id]);
				/* HjSync */
				return make_json(1, '操作成功');
			}
			if(input('post.submit4')) {
				$post['check_status'] = 2;
				model('Merchant')->allowField(true)->save($post, ['merchant_id' => $merchant_id]);
				model('Config')->config('mail_switch') && \think\Queue::push('\app\pay\job\AliSend@audit_merchant', ['agent_id' => $value['agent_id'], 'merchant_id' => $value['merchant_id']]);
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

	public function set_name($merchant_id, $merchant_name)
	{
		model('Merchant')->allowField(true)->save(['merchant_name' => $merchant_name], ['merchant_id' => $merchant_id]);
		return make_json(1, '操作成功');
	}

	public function status($merchant_id)
	{
		$value = model('Merchant')->get_one($merchant_id);
		if(request()->isPost()) {
			if(0 == number($value['trade_rates'])) {
				return make_json(0, '请先设置费率');
			}
			$post = [];
			if($value['status'] == 1) {
				$post['status'] = 0;
			} else {
				$post['status'] = 1;
			}
			model('Merchant')->allowField(true)->save($post, ['merchant_id' => $merchant_id]);
			return make_json(1, '操作成功');
		}
	}

	public function trade_rates($merchant_id)
	{
		$value = model('Merchant')->get_one($merchant_id);
		if(request()->isPost()) {
			$post = [];
			$post['trade_rates'] = input('post.trade_rates');
			model('Merchant')->allowField(true)->save($post, ['merchant_id' => $merchant_id]);
			return make_json(1, '操作成功');
		}
	}

	public function upload($merchant_id = null)
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

	public function checkf($merchant_id)
	{
		$title = input('param.title');
		$file = input('param.file');
		if(!is_file(file_path($file))) {
			return make_json(0, '文件不存在');
		} else {
			return make_json(1, url('merchant/download', ['merchant_id' => $merchant_id, 'file' => $file, 'title' => $title]));
		}
	}

	public function download($merchant_id)
	{
		$title = input('param.title');
		$file = input('param.file');
		$filepath = file_path($file);
		if(!is_file($filepath)) {
			echo '文件不存在';
			exit();
		}
		$filesize = filesize($filepath);
		if(empty($title)) {
			$filename = Db::name('merchant')->where('merchant_id', '=', $merchant_id)->value('merchant_name') . '.' . $this->get_ext($file);
		} else {
			$filename = Db::name('merchant')->where('merchant_id', '=', $merchant_id)->value('merchant_name'). '_' . $title . '.' . $this->get_ext($file);
		}
		header("Cache-Control: no-cache, must-revalidate");
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: {$filesize}");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Connection: close");
		readfile($filepath);
	}

	public function gates($merchant_id)
	{
		$file_list = _dir(APP_PATH . 'common/subpay/');
		$gate_list = [];
		foreach($file_list as $key => $val) {
			if($val['type'] == 'file' && preg_match('/\.php$/', $val['name'])) {
				$file = preg_replace('/\.php$/', '', basename($val['name']));
				$class = '\\app\\common\\subpay\\' . $file;
				$sub_key = strtolower($file);
				$gate_list[$sub_key] = [
					'sub_key' => $sub_key,
					'sub_name' => $class::NAME
				];
			}
		}
		$value = Db::name('merchant')
			->alias('m')
			->join('merchant_weixin mw', 'm.merchant_id = mw.merchant_id', 'LEFT')
			->join('merchant_alipay ma', 'm.merchant_id = ma.merchant_id', 'LEFT')
			->where('m.merchant_id', '=', $merchant_id)
			->field('m.merchant_id, m.agent_id, m.status, m.trade_rates, mw.sub_mch_id, mw.trade_rates as rates_weixin, ma.app_auth_token, ma.trade_rates as rates_alipay, mw.sub_gate as gate_weixin, ma.sub_gate as gate_alipay, mw.status as status_weixin, ma.status as status_alipay')
			->find();
		if(0 == number($value['trade_rates'])) {
			echo '请先配置默认费率';
			exit();
		}
		if(request()->isPost()) {
			$post = input('post.');
			if($post['status_weixin'] && empty($post['gate_weixin'])) {
				Db::name('merchant_weixin')->where('merchant_id', '=', $merchant_id)->update([
					'sub_mch_id' => $post['sub_mch_id'],
				]);
			}
			if($post['status_alipay'] && empty($post['gate_alipay'])) {
				Db::name('merchant_alipay')->where('merchant_id', '=', $merchant_id)->update([
					'app_auth_token' => $post['app_auth_token'],
				]);
			}
			if($post['gate_weixin'] && $post['gate_alipay']) {
				if($post['weixin_sub_mch_no'] != $post['alipay_sub_mch_no']) {
					return make_json(0, $gate_list[$post['gate_weixin']]['sub_name'] . '商户编号不一致');
				}
				if($post['weixin_trade_rates'] != $post['alipay_trade_rates']) {
					return make_json(0, $gate_list[$post['gate_weixin']]['sub_name'] . '交易费率不一致');
				}
			}
			if($post['status_weixin'] && $post['gate_weixin']) {
				if(empty($post['weixin_sub_mch_no'])) {
					return make_json(0, '微信间连通道未配置');
				}
				$gate_weixin = model('Gates')->get_one(['merchant_id' => $merchant_id, 'sub_gate' => $post['gate_weixin']]);
				if(empty($gate_weixin)) {
					model('Gates')->allowField(true)->save([
						'merchant_id' => $merchant_id,
						'sub_gate' => $post['gate_weixin'],
						'sub_mch_no' => $post['weixin_sub_mch_no'],
						'trade_rates' => $post['weixin_trade_rates'],
						'time_create' => _time(),
					]);
				} else {
					model('Gates')->allowField(true)->save([
						'sub_mch_no' => $post['weixin_sub_mch_no'],
						'trade_rates' => $post['weixin_trade_rates'],
						'time_update' => _time(),
					], ['id' => $gate_weixin['id']]);
				}
			}
			if($post['status_alipay'] && $post['gate_alipay']) {
				if(empty($post['alipay_sub_mch_no'])) {
					return make_json(0, '支付宝间连通道未配置');
				}
				$gate_alipay = model('Gates')->get_one(['merchant_id' => $merchant_id, 'sub_gate' => $post['gate_alipay']]);
				if(empty($gate_alipay)) {
					model('Gates')->allowField(true)->save([
						'merchant_id' => $merchant_id,
						'sub_gate' => $post['gate_alipay'],
						'sub_mch_no' => $post['alipay_sub_mch_no'],
						'trade_rates' => $post['alipay_trade_rates'],
						'time_create' => _time(),
					]);
				} else {
					model('Gates')->allowField(true)->save([
						'sub_mch_no' => $post['alipay_sub_mch_no'],
						'trade_rates' => $post['alipay_trade_rates'],
						'time_update' => _time(),
					], ['id' => $gate_alipay['id']]);
				}
			}
			Db::name('merchant_weixin')->where('merchant_id', '=', $merchant_id)->update([
				'status' => $post['status_weixin'],
				'sub_gate' => $post['gate_weixin'] ? $post['gate_weixin'] : '',
				'trade_rates' => (number($value['trade_rates']) != number($post['rates_weixin'])) ? $post['rates_weixin'] : '0.00',
			]);
			Db::name('merchant_alipay')->where('merchant_id', '=', $merchant_id)->update([
				'status' => $post['status_alipay'],
				'sub_gate' => $post['gate_alipay'] ? $post['gate_alipay'] : '',
				'trade_rates' => (number($value['trade_rates']) != number($post['rates_alipay'])) ? $post['rates_alipay'] : '0.00',
			]);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function get_rates($merchant_id)
	{
		$value = Db::name('merchant')->where('merchant_id', '=', $merchant_id)->field('trade_rates')->find();
		if(!$value) {
			$value = ['trade_rates' => '0.00'];
		}
		return make_json(1, 'ok', $value);
	}

	public function get_sub_gate($merchant_id, $sub_gate = '')
	{
		$value = Db::name('gates')->where('merchant_id', '=', $merchant_id)->where('sub_gate', '=', $sub_gate)->field('status, sub_mch_no, trade_rates')->find();
		if(!$value) {
			$value = [
				'status' => 0,
				'sub_mch_no' => '',
				'trade_rates' => '0.00',
			];
		}
		return make_json(1, 'ok', $value);
	}

	public function bind_wechat($merchant_id)
	{
		if(request()->isPost()) {
			echo url('/mp/bind/wechat', ['merchant_id' => authcode($merchant_id, 'ENCODE', '', 300)], null, true);
		}
	}

	public function unbind_wechat($merchant_id)
	{
		if(request()->isPost()) {
			model('Merchant')->allowField(true)->save(['openid' => ''], ['merchant_id' => $merchant_id]);
			return make_json(1, '操作成功');
		}
	}

}


<?php

namespace app\agent\controller;

use \think\Db;

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
		$where['m.agent_id'] = ['=', $this->agent['agent_id']];
		if(input('param.wd')) {
			$where['m.merchant_no|m.merchant_name'] = ['LIKE', '%'.input('param.wd').'%'];
		}
		if(input('param.merchant_id')) {
			$where['m.merchant_id'] = ['=', input('param.merchant_id')];
		} else {
			if(input('param.merchant_name')) {
				$where['m.merchant_no|m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
			}
		}
		$where['m.check_status'] = ['=', '-1'];
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
			$where['m.agent_id'] = ['=', $this->agent['agent_id']];
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
		$where['m.agent_id'] = ['=', $this->agent['agent_id']];
		if(input('param.wd')) {
			$where['m.merchant_no|m.merchant_name'] = ['LIKE', '%'.input('param.wd').'%'];
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
			->where('m.agent_id', '=', $this->agent['agent_id'])
			->order('m.merchant_id', 'DESC')
			->field('m.*, a.agent_no, a.agent_name')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
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

	public function add()
	{
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_create'] = _time();
			if(Db::name('merchant')->where('per_phone', '=', $post['per_phone'])->count()) {
				return make_json(0, '负责人电话已经存在');
			}
			$post['agent_id'] = $this->agent['agent_id'];
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
			->join('account a', 'm.merchant_id = a.account_id', 'LEFT')
			->where('m.agent_id', '=', $this->agent['agent_id'])
			->where('m.merchant_id', '=', $merchant_id)
			->field('m.*, a.*')
			->find();
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_update'] = _time();
			if(Db::name('merchant')->where('per_phone', '=', $post['per_phone'])->where('merchant_id', '<>', $merchant_id)->count()) {
				return make_json(0, '负责人电话已经存在');
			}
			if(input('post.submit2')) {
				model('Account')->allowField(true)->save($post, ['account_id' => $merchant_id]);
				model('Merchant')->allowField(true)->save($post, ['merchant_id' => $merchant_id]);
				/* HjSync */
				//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@merchant', ['merchant_id' => $merchant_id]);
				/* HjSync */
				return make_json(1, '保存成功');
			}
			if(input('post.submit3')) {
				$post['check_status'] = 1;
				model('Account')->allowField(true)->save($post, ['account_id' => $merchant_id]);
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

}


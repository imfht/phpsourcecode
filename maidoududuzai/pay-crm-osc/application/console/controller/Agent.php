<?php

namespace app\console\controller;

use \think\Db;

class Agent
{

	public $admin;
	public $AgentLevel;

	public function __construct()
	{
		$this->admin = model('Admin')->checkLoginAdmin();
		$this->AgentLevel = model('AgentLevel')->getLevel();
	}

	public function index()
	{
		$where = [];
		if(input('param.up_id')) {
			$where['a.up_id'] = ['=', input('param.up_id')];
		}
		if(input('param.wd')) {
			$where['a.agent_no|a.agent_name'] = ['LIKE', '%'.input('param.wd').'%'];
		}
		if(input('param.agent_id')) {
			$where['a.agent_id'] = ['=', input('param.agent_id')];
		} else {
			if(input('param.agent_name')) {
				$where['a.agent_no|a.agent_name'] = ['LIKE', '%'.input('param.agent_name').'%'];
			}
		}
		$object = Db::name('agent')
			->alias('a')
			->join('wx_user wx', 'a.openid = wx.openid', 'LEFT')
			->where($where)
			->field('a.*, wx.nickname, wx.headimgurl')
			->order('agent_id', 'DESC')
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
			if(input('param.up_id')) {
				$where['a.up_id'] = ['=', input('param.up_id')];
			}
			if(input('param.agent_id')) {
				$where['a.agent_id'] = ['=', input('param.agent_id')];
			} else {
				if(input('param.agent_name')) {
					$where['a.agent_no|a.agent_name'] = ['LIKE', '%'.input('param.agent_name').'%'];
				}
			}
			$list = Db::name('agent')
				->alias('a')
				->join('agent p', 'a.up_id = p.agent_id', 'LEFT')
				->where($where)
				->field('p.agent_no as up_no, p.agent_name as up_name, a.agent_no, a.agent_name, a.per_name, a.per_phone, a.province, a.city, a.county, a.address, a.time_create')
				->order('a.agent_id', 'ASC')
				->select();
			$data = [];
			$i = 0;
			$i++;
			$data[$i] = ['序号', '代理商编号', '代理商名称', '父代理编号', '父代理名称', '联系人', '联系电话', '地区', '地址', '创建时间'];
			foreach($list as $val) {
				$i++;
				$data[$i] = [$i - 1, $val['agent_no'], $val['agent_name'], $val['up_no'], $val['up_name'], $val['per_name'], $val['per_phone'], $val['province'] . $val['city'] . $val['county'], $val['address'], gsdate('Y-m-d H:i:s', $val['time_create'])];
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
			$Excel->getActiveSheet()->getColumnDimension('B')->setWidth(15); //代理商编号
			$Excel->getActiveSheet()->getColumnDimension('C')->setWidth(30); //代理商名称
			$Excel->getActiveSheet()->getColumnDimension('D')->setWidth(15); //父代理编号
			$Excel->getActiveSheet()->getColumnDimension('E')->setWidth(30); //父代理名称
			$Excel->getActiveSheet()->getColumnDimension('F')->setWidth(15); //联系人
			$Excel->getActiveSheet()->getColumnDimension('G')->setWidth(15); //联系电话
			$Excel->getActiveSheet()->getColumnDimension('H')->setWidth(30); //地区
			$Excel->getActiveSheet()->getColumnDimension('I')->setWidth(30); //地址
			$Excel->getActiveSheet()->getColumnDimension('J')->setWidth(20); //创建时间
			header("Cache-Control: no-cache, must-revalidate");
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=代理列表_" . gsdate('Y-m-d') . ".xls");
			$Writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
			$Writer->save('php://output');
			exit();
		}
		include \befen\view();
	}

	public function get_id_authcode($agent_id)
	{
		return make_json(1, 'ok', ['agent_id' => authcode($agent_id, 'ENCODE', '', 60)]);
	}

	public function add()
	{
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_create'] = _time();
			if(Db::name('agent')->where('per_phone', '=', $post['per_phone'])->count()) {
				return make_json(0, '联系电话已经存在');
			}
			$post['up_id'] = 0;
			$post['agent_status'] = 0;
			if($this->AgentLevel[$post['level_id']]['join_cost'] == 0) {
				$post['agent_status'] = 1;
			}
			$password = get_rand(12);
			$post['password'] = authcode($password, 'ENCODE');
			model('Agent')->allowField(true)->save($post);
			$agent_id = model('Agent')->getLastInsID();
			$agent_no = $agent_id + pow(10, 7);
			model('Agent')->allowField(true)->save(['agent_no' => $agent_no], ['agent_id' => $agent_id]);
			\think\Queue::push('\app\pay\job\AliSend@add_agent', ['agent_id' => $agent_id]);
			return make_json(1, '添加代理成功', [
				'agent_id' => $agent_id,
				'agent_no' => $agent_no,
				'agent_password' => $password,
			]);
		}
		include \befen\view();
	}

	public function detail($agent_id)
	{
		$value = model('Agent')->get_one($agent_id);
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_update'] = _time();
			if(Db::name('agent')->where('per_phone', '=', $post['per_phone'])->where('agent_id', '<>', $agent_id)->count()) {
				return make_json(0, '联系电话已经存在');
			}
			model('Agent')->allowField(true)->save($post, ['agent_id' => $agent_id]);
			return make_json(1, '编辑代理成功');
		}
		include \befen\view();
	}

	public function profit()
	{
		$where = [];
		if(input('param.agent_id')) {
			$where['up.agent_id'] = ['=', input('param.agent_id')];
		} else {
			if(input('param.agent_name')) {
				$where['up.agent_no|up.agent_name'] = ['LIKE', '%'.input('param.agent_name').'%'];
			}
		}
		$object = Db::name('agent_profit')
			->alias('ap')
			->join('agent up', 'ap.up_id = up.agent_id', 'LEFT')
			->join('agent me', 'ap.agent_id = me.agent_id', 'LEFT')
			->where($where)
			->order('ap.id', 'DESC')
			->field('ap.*, up.agent_no as up_no, up.agent_name as up_name, me.level_id, me.agent_no as agent_no, me.agent_name as agent_name')
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

	public function passwd($agent_id)
	{
		if(request()->isPost()) {
			$value = model('Agent')->get_one($agent_id);
			if(input('post.password') == authcode($value['password'], 'DECODE')) {
				return make_json(0, '密码与原密码相同');
			}
			$post = [];
			$post['password'] = authcode(input('post.password'), 'ENCODE');
			model('Agent')->allowField(true)->save($post, ['agent_id' => $agent_id]);
			model('Config')->config('sms_switch') && \think\Queue::push('\app\pay\job\AliSend@reset_password', ['agent_id' => $agent_id]);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function status($agent_id)
	{
		$value = model('Agent')->get_one($agent_id);
		if(request()->isPost()) {
			$post = [];
			if($value['agent_status'] == 1) {
				$post['agent_status'] = 0;
			} else {
				$post['agent_status'] = 1;
			}
			model('Agent')->allowField(true)->save($post, ['agent_id' => $agent_id]);
			return make_json(1, '操作成功');
		}
	}

	public function join_cost($agent_id)
	{
		$value = model('Agent')->get_one($agent_id);
		if(request()->isPost()) {
			$post = [];
			$post['join_cost'] = input('post.join_cost');
			model('Agent')->allowField(true)->save($post, ['agent_id' => $agent_id]);
			$up_id = $value['up_id'];
			if($up_id) {
				$up_agent = model('Agent')->get_one($up_id);
				$commission = $post['join_cost'] / 100 * $this->AgentLevel[$up_agent['level_id']]['join_rates'];
				$commission = substr(sprintf('%.1f', $commission), 0, -2);
				$agent_profit = Db::name('agent_profit')->where('agent_id', '=', $agent_id)->count();
				if($commission > 0 && $agent_profit == 0) {
					Db::name('agent_profit')->insert([
						'up_id' => $up_id,
						'agent_id' => $agent_id,
						'y' => gsdate('Y'),
						'm' => gsdate('m'),
						'd' => gsdate('d'),
						'time_create' => _time(),
						'commission' => $commission,
					]);
				}
			}
			return make_json(1, '操作成功');
		}
	}

	public function bind_wechat($agent_id)
	{
		if(request()->isPost()) {
			echo url('/mo/bind/wechat', ['agent_id' => authcode($agent_id, 'ENCODE', '', 300)], null, true);
		}
	}

	public function unbind_wechat($agent_id)
	{
		if(request()->isPost()) {
			model('Agent')->allowField(true)->save(['openid' => ''], ['agent_id' => $agent_id]);
			return make_json(1, '操作成功');
		}
	}

}


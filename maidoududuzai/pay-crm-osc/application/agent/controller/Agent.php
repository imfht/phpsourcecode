<?php

namespace app\agent\controller;

use \think\Db;

class Agent
{

	public $agent;
	public $AgentLevel;

	public function __construct()
	{
		$this->agent = model('Agent')->checkLoginAgent();
		$this->AgentLevel = model('AgentLevel')->getLevel();
	}

	public function index()
	{
		$where = [];
		$where['a.up_id'] = ['=', $this->agent['agent_id']];
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
			->where($where)
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
			$where['a.up_id'] = ['=', $this->agent['agent_id']];
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

	public function add()
	{
		$this->check_level();
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_create'] = _time();
			if(Db::name('agent')->where('per_phone', '=', $post['per_phone'])->count()) {
				return make_json(0, '联系电话已经存在');
			}
			$post['up_id'] = $this->agent['agent_id'];
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
		$this->check_level();
		$value = model('Agent')->get_one($agent_id);
		if(request()->isPost()) {
			if($value['join_cost']) {
				return make_json(0, '非法操作');
			} else {
				$post = input('post.');
				$post['time_update'] = _time();
				if(Db::name('agent')->where('per_phone', '=', $post['per_phone'])->where('agent_id', '<>', $agent_id)->count()) {
					return make_json(0, '联系电话已经存在');
				}
				model('Agent')->allowField(true)->save($post, ['agent_id' => $agent_id]);
				return make_json(1, '编辑代理成功');
			}
		}
		include \befen\view();
	}

	public function profit()
	{
		$where = [];
		$where['ap.up_id'] = ['=', $this->agent['agent_id']];
		if(input('param.agent_id')) {
			$where['agent_id'] = ['=', input('param.agent_id')];
		} else {
			if(input('param.agent_name')) {
				$where['agent_no|agent_name'] = ['LIKE', '%'.input('param.agent_name').'%'];
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

	protected function check_level()
	{
		if(request()->isPost()) {
			if(input('post.level_id') <= $this->agent['level_id']) {
				return make_json(0, '非法操作');
				exit();
			}
		}
		foreach($this->AgentLevel as $key => $val) {
			if($key <= $this->agent['level_id']) {
				unset($this->AgentLevel[$key]);
			}
		}
		if(!$this->AgentLevel) {
			echo '对不起，您不能发展代理！';
			exit();
		}
	}

}


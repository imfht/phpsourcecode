<?php

namespace app\mo\controller;

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
		if(input('param.wd')) {
			$where['agent_no|agent_name'] = ['LIKE', '%'.\urldecode(input('param.wd')).'%'];
		}
		if(input('param.agent_id')) {
			$where['up_id'] = ['=', input('param.agent_id')];
		}
		if(input('param.agent_name')) {
			$where['agent_name'] = ['LIKE', '%'.input('param.agent_name').'%'];
		}
		if(request()->isAjax()){
			$object = Db::name('agent')
				->where($where)
				->where('up_id', '=', $this->agent['agent_id'])
				->order('agent_id', 'DESC')
				->paginate(20, false, ['query' => request()->param()])
				->each(function($item, $key){
					$item['agent_status_text'] = model('Agent')::getStatus($item['agent_status']);
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
			$res = [
				'agent_id' => model('Agent')->getLastInsID(),
				'agent_password' => $password,
			];
			$res['agent_no'] = $res['agent_id'] + pow(10, 7);
			model('Agent')->allowField(true)->save(['agent_no' => $res['agent_no']], ['agent_id' => $res['agent_id']]);
			return make_json(1, '添加代理成功', $res);
		}
		include \befen\view();
	}

	public function detail($agent_id) {
		$this->check_level();
		$value = model('Agent')->get_one($agent_id);
		if($value['join_cost']){
			$value['join_cost'] = $value['join_cost'] . '(实缴)';
		}else{
			$value['join_cost'] = $this->AgentLevel[$value['level_id']]['join_cost'] . '(默认)';
		}
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
		$wd = input('param.wd/s');
		if($wd) {
			$where['me.agent_name|me.agent_no'] = ['LIKE', '%'.urldecode($wd).'%'];
		}
		if(request()->isAjax()){
			$object = Db::name('agent_profit')
				->alias('ap')
				->join('agent up', 'ap.up_id = up.agent_id', 'LEFT')
				->join('agent me', 'ap.agent_id = me.agent_id', 'LEFT')
				->where($where)
				->order('ap.id', 'DESC')
				->field('ap.*, up.agent_no as up_no, up.agent_name as up_name, me.level_id, me.agent_no as agent_no, me.agent_name as agent_name')
				->paginate(20, false, ['query' => request()->param()])
				->each(function($item, $key){
					$item['level_name'] = $this->AgentLevel[$item['level_id']]['level_name'];
					$item['time_status_text'] = $item['time_status'] ? gsdate('Y-m-d H:i', $item['time_status']) : '未结算';
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


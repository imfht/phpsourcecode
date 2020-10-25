<?php

namespace app\agent\controller;

use \think\Db;

class Profile
{

	public $agent;

	public function __construct()
	{
		$this->agent = model('Agent')->checkLoginAgent();
		$this->AgentLevel = model('AgentLevel')->getLevel();
	}

	public function index()
	{
		$value = Db::name('agent a')
			->join('agent_level al', 'a.level_id = al.level_id', 'LEFT')
			->where('a.agent_id', '=', $this->agent['agent_id'])
			->field('a.*, al.*')
			->find();
		include \befen\view();
	}

}


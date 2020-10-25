<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\api;

use app\model\Config as ConfigM;

/**
 * @title 基础功能
 */
class Config extends Base {

	/**
	 * @title 配置数据
	 * @param  ConfigM $config [description]
	 * @return [type]          [description]
	 */
	public function index(ConfigM $config) {
		$param = $this->request->param();
		$parse = isset($param['parse']) ? $param['parse'] : 1;

		if (isset($param['parse']) && $param['parse'] == 1) {
			$list               = $config->getConfigList($this->request);
			$this->data['data'] = $list;
		}else{
			$list               = $config->getConfig($this->request);
			$this->data['data'] = $list;
		}
		return $this->data;
	}

	/**
	 * @title 配置数据（树）
	 * @param  ConfigM $config [description]
	 * @return [type]          [description]
	 */
	public function tree(ConfigM $config) {
		$list               = $config->getConfigTree($this->request);
		$this->data['data'] = $list;
		return $this->data;
	}

	/**
	 * @title 配置更新
	 * @param  ConfigM $config [description]
	 * @return [type]          [description]
	 */
	public function save(ConfigM $config) {
		$data = $this->request->post();

		foreach ($data as $key => $value) {
			$config->update(['value' => $value], ['name' => $key]);
		}

		$this->data['code'] = 1;
		$this->data['msg'] = "更新成功！";
		return $this->data;
	}
}
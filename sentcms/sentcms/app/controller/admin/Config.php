<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use app\model\Config as ConfigM;
use think\facade\Cache;

/**
 * @title 配置管理
 */
class Config extends Base {

	public function _initialize() {
		parent::_initialize();
		$this->model = new ConfigM();
	}

	/**
	 * @title 配置管理
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function index(ConfigM $config) {
		$param = $this->request->param();


		$group = input('group', 0, 'trim');
		$name = input('name', '', 'trim');
		/* 查询条件初始化 */
		$map = array('status' => 1);
		if ($group) {
			$map['group'] = $group;
		}

		if ($name) {
			$map['name'] = array('like', '%' . $name . '%');
		}

		$list = $config->where($map)->order('id desc')->paginate(25, false, array(
			'query' => $this->request->param(),
		));
		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);

		$this->data = array(
			'group' => config('config_group_list'),
			'config_type' => config('config_config_list'),
			'page' => $list->render(),
			'group_id' => $group,
			'list' => $list,
		);

		return $this->fetch();
	}

	/**
	 * @title 信息配置
	 */
	public function group(ConfigM $config, $id = 1) {
		if ($this->request->isPost()) {
			$data = $this->request->post();

			foreach ($data['config'] as $key => $value) {
				ConfigM::update(['value' => $value], ['name' => $key]);
			}
			//清除db_config_data缓存
			Cache::pull('system_config_data');
			return $this->success("更新成功！");
		} else {
			$list = $config->where(array('status' => 1, 'group' => $id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
			if ($list) {
				$this->data['list'] = $list;
			}
			$this->data['id'] = $id;
			return $this->fetch();
		}
	}

	/**
	 * @title 新增配置
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function add(ConfigM $config) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			if ($data) {
				$result = ConfigM::create($data);
				if (false !== $result) {
					Cache::pull('system_config_data');
					return $this->success('新增成功', url('/admin/config/index'));
				} else {
					return $this->error('新增失败');
				}
			} else {
				return $this->error('无添加数据！');
			}
		} else {
			$this->data['info'] = [];
			return $this->fetch('edit');
		}
	}

	/**
	 * @title 编辑配置
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function edit($id = 0) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			if ($data) {
				$result = ConfigM::update($data, array('id' => $data['id']));
				if (false !== $result) {
					Cache::pull('system_config_data');
					return $this->success('更新成功', Cookie('__forward__'));
				} else {
					return $this->error('更新失败！');
				}
			} else {
				return $this->error('无更新数据！');
			}
		} else {
			$info = array();
			/* 获取数据 */
			$info = ConfigM::find($id);

			if (false === $info) {
				return $this->error('获取配置信息错误');
			}
			$this->data = ['info' => $info];
			return $this->fetch();
		}
	}
	/**
	 * @title 批量保存配置
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function save($config) {
		if ($config && is_array($config)) {
			foreach ($config as $name => $value) {
				(new ConfigM())->save(['value' => $value], ['name' => $name]);
			}
		}
		Cache::pull('system_config_data');
		return $this->success('保存成功！');
	}

	/**
	 * @title 删除配置
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function del() {
		$id = $this->request->param('id');

		if (empty($id)) {
			return $this->error('请选择要操作的数据!');
		}

		$result = ConfigM::find($id)->delete();
		if (false !== $result) {
			Cache::pull('system_config_data');
			return $this->success('删除成功');
		} else {
			return $this->error('删除失败！');
		}
	}

	/**
	 * @title 主题选择
	 */
	public function themes(ConfigM $config) {
		$list = $config->getThemesList($this->request);
		$config = Cache::get('system_config_data');
		
		$this->data = array(
			'pc' => isset($config['pc_themes']) ? $config['pc_themes'] : '',
			'mobile' => isset($config['mobile_themes']) ? $config['mobile_themes'] : '',
			'list' => $list,
		);
		return $this->fetch();
	}

	/**
	 * @title 设置主题
	 * @return json
	 */
	public function setthemes($name, $id) {
		$result = ConfigM::update(['value' => $id], ['name' => $name."_themes"]);
		if (false !== $result) {
			Cache::pull('system_config_data');
			return $this->success('设置成功！');
		} else {
			return $this->error('设置失败！');
		}
	}
}
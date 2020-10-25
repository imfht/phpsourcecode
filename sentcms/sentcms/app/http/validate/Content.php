<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\http\validate;

use think\Validate;
use think\facade\Request;
use app\model\Attribute;

/**
 * 菜单验证
 */
class Content extends Validate{
	protected $rule = [];

	protected $message  =   [];

	protected $scene = []; 

	public function __construct(){
		parent::__construct();
		$param = Request::param();
		$map = [];

		$map[] = ['model_id', '=', $param['model_id']];
		if ($param['function'] == 'add') {
			$map[] = ['is_show', 'IN', [1, 2]];
		}else if ($param['function'] == 'edit') {
			$map[] = ['is_show', 'IN', [1, 3]];
		}

		$attr = Attribute::where($map)->select();
		$rule = [];
		$message = [];
		$field = [];
		foreach ($attr as $value) {
			if ($value['is_must']) {
				$rule[$value['name']] = 'require';
				$message[$value['name'].'.require'] = $value['title'] . '不能为空！';
				$field[] = $value['name'];
			}
		}
		$this->rule = $rule;

		$this->message = $message;

		$this->scene = [
			'adminadd' => $field,
			'adminedit' => $field,
			'useradd' => $field,
			'useredit' => $field,
		];
	}
}
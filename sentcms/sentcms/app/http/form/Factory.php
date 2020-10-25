<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\http\form;

use think\facade\View;

/**
 * @title 后台中间件
 */
class Factory {

	protected $field = [];
	protected $data = [];

	public function __construct($field, $data){
		$field['is_must'] = isset($field['is_must']) ? $field['is_must'] : 0;
		$this->field = $field;
		$this->data  = $data;
		$this->parseValue();
	}

	public function display($template = 'show', $data = []){
		View::config([
			'view_path' => dirname(__file__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR
		]);
		View::assign($data);
		return View::fetch('/' . $template, $this->field);
	}

	protected function parseValue(){
		$this->field['value'] = isset($this->data[$this->field['name']]) ? $this->data[$this->field['name']] : (isset($this->field['value']) ? $this->field['value'] : '');
	}

	public function show(){
		return $this->display($this->field['type']);
	}
}
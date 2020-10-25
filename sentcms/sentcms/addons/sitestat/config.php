<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

return [
	['name' => 'title', 'title' => '显示标题', 'type' => 'text', 'value' => '系统信息'],
	['name' => 'width', 'title' => '显示宽度', 'type' => 'select', 'value' => '12', 'option' => [
		['key'=>'4', 'label'=>'4格'],
		['key'=>'6', 'label'=>'6格'],
		['key'=>'12', 'label'=>'12格']
	]],
	['name' => 'display', 'title' => '是否显示', 'type' => 'select', 'value' => '1', 'option' => [
		['key'=>'1', 'label'=>'显示'],
		['key'=>'0', 'label'=>'不显示']
	]],
];
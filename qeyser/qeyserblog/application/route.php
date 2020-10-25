<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
	// 首页路由
	'/'       =>  'index/index/index',
	// 搜索页面
	'search'  =>  'index/search/keywords',
	// 友情连接路由
	'friendlink'    =>  'index/friendlink/index', 
	// 分类路由
	'category/:cid' =>  ['index/article/category', ['method' => 'get'] ,['cid' => '\d+']],
	// 阅读路由
	'article/:aid'  =>  ['index/article/view', ['method' => 'get'] ,['aid' => '\d+']],
	// 分页路由
	'cate/:cid/:page'   =>  ['index/article/category', ['method' => 'get'] ,['page' => '\d+']]
];

<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Blog\Widget;
use Think\Action;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class PositionWidget extends Action{
	
	/* 显示指定分类的同级分类或子分类列表 */
	public function lists($position=4,$category,$limit=5,$filed){


        /**
         * 获取推荐位数据列表
         * @param  number  $pos      推荐位 1-列表推荐，2-频道页推荐，4-首页推荐
         * @param  number  $category 分类ID
         * @param  number  $limit    列表行数
         * @param  boolean $filed    查询字段
         * @return array             数据列表
         */
		$lists=D('Document')->position($position,$category,$limit,$filed);
		$this->assign('lists', $lists);
		$this->display('Widget/position');
	}
	
}

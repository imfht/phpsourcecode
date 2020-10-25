<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
 
namespace osc\mobile\controller;
use osc\common\controller\Base;
use think\Db;
class Article extends Base
{
 	function index(){
 		
		$id=(int)input('param.id');
		
		if(in_wechat())
		$this->assign('signPackage',wechat()->getJsSign(request()->url(true)));	
		
		$this->assign('article',Db::name('wechat_news_reply')->where('nr_id',$id)->find());
		
        return $this->fetch();
    }
	
}

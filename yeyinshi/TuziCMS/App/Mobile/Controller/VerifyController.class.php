<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Mobile\Controller;
use Think\Controller;
use Common\Lib\String; //引入类函数
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
class VerifyController extends Controller {
	/**
	 * 验证码实现
	 */
    public function code(){
    	//$w=$_GET['w'];//标签库文件里定义了这个w变量了__APP__/Public/verify?w={$width}&h={$height}
    	$w=I('get.w');
    	$h=I('get.h');
    	//$h=$_GET['h'];//标签库文件里定义了这个h变量了__APP__/Public/verify?w={$width}&h={$height}
    	import('ORG.Util.Image');//引入图片类文件
    	Image::buildImageVerify(4,1,'png',$w,$h,'verify');
    	//生成图片验证代码 0 字母 1 数字 2 大写字母 3 小写字母 4中文 5混合  verify是验证码的SESSION记录名称
    }

}

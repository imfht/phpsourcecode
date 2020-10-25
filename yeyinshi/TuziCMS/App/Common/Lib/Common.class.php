<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Common\Lib;
class Common {
	
    /**
     * 截取内容中第一张图片函数
     */
    static public function catch_that_image($str) {
    	// 正则匹对图片
    	
    	preg_match('/<img\s[^<>]*?src=[\'\"]([^\'\"<>]+?)[\'\"][^<>]*?>/i', $str, $matche);
    	if($matche[1])
    		return $matche[1];
    	//否则返回false
    	return '';
    }

    /**
     * 过滤html src标签和截取中文函数
     */
	static public function substr_ext($str, $start=0, $length, $charset="utf-8", $suffix=""){
		if(function_exists("mb_substr")){
			return strip_tags(mb_substr($str, $start, $length, $charset).$suffix);
		}
		elseif(function_exists('iconv_substr')){
			return strip_tags(iconv_substr($str,$start,$length,$charset).$suffix);
		}
		$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
		return strip_tags($slice.$suffix);
	}
    
    
    /**
     * 显示友情链接
     */
    static public function link(){
    	$m=D('Link');
    	$arr=$m->where("link_show=0")->order('link_sort')->select();
    	//只显示未被删除news_dell=0的数据
    	dump($arr);
    	//exit;
    	 
    	$this->assign('vlink',$arr);
    	$this->display();
    }


    /**
     * 点击数递增函数
     */
    static public function clicknum(){
    	$id=$_GET['id'];
    	//echo $id;
    	//**点击数递增
    	$news_hits=M('News')->where(array('id'=>$id))->getField('news_hits');
    	D('News')->where(array('id'=>$id))->setInc('news_hits');
    
    	echo 'document.write(' . $news_hits . ')';
    }
    
    
    /**
     * 自动登录后，js验证，更新积分
     */
    static public function loginChk() {
    	//echo 1112;
    	//exit;
    	if (!IS_AJAX) exit();
    
    	$uid=intval($_SESSION['uid']);
    	$email=$_SESSION['user_email'];
    	$nickname=$_SESSION['user_name'];
    	//echo $email;
    	//exit;
    
    	$furl = '';
    
    	$nickname = empty($nickname)? $email : $nickname;
    	//$nickname不为空的情况下是否等于$email，否则$nickname等于$nickname
    
    	if ($uid <= 0 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
	    	session('user_name',null);
	    	session('user_email',null);
	    	session('uid',null);
    		$this->error('请登录', '');//支持ajax,$this->error(info,url,array);
    	}
    
    	$this->success('已登录', $furl , array('nickname'=>$nickname));
    }
    
    /**
     * 删除文件的函数
     */
    static public function delete_file($dir){
    	$list = scandir($dir); // 得到该文件下的所有文件和文件夹
    	foreach($list as $file){//遍历
    		$file_location=$dir."/".$file;//生成路径
    		if(is_dir($file_location) && $file!="." &&$file!=".."){ //判断是不是文件夹
    			echo "------------------------sign in $file_location------------------";
    			delete_file($file_location); //继续遍历
    		}else if($file!="."&&$file!=".."){
	    		$str = "group";//指定字符串
	    		 
	    		if(substr_count($file,$str)>0){//如果文件名包含该字符串
	    		unlink($file);
	    		}
    		}
    	}
    }
  
}
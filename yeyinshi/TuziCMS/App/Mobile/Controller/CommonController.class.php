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
class CommonController extends Controller {
   
    /**
     * SEO赋值
     */
    public function seo($title,$keywords,$description,$positioin){
    	$this->assign('title',$title);
    	$this->assign('keywords',$keywords);
    	$this->assign('description',$description);
    	$this->assign('position',$positioin); 
    }
    
    /**
     * 截取内容中第一张图片函数
     */
    function catch_that_image($str) {
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
	function substr_ext($str, $start=0, $length, $charset="utf-8", $suffix=""){
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
     * 显示文章
     */
    public function detail(){
    	//**查询具体id的文章
    	$id=I('get.id');
		if (!preg_match('/^\d+$/i', $id)){
				$this->error('url参数错误');
				exit;//仿制用户恶意输出url参数
		}
//     	dump($id);
//     	exit;
    	if ($id==""){
    		$this->error('参数错误');
    	}
    	
    	$m=D('News');
    	$arr=$m->relation(true)->find($id);//查询出具体id的文章，获取该文章所在的栏目id数值nv_id

    	$iarr = $this->substr_ext($arr['news_title'], 0, 220, 'utf-8',"");
    	$arr['news_title']=$iarr;
    	unset($iarr);
    	$arr['news_content'] = htmlspecialchars_decode($arr['news_content']); //转译过滤字符
    	$arr['news_pic'] = __ROOT__.$arr['news_pic'];
    	
    	$download=$arr['news_download'];
    	if ($download==null){
    		$arr['news_download'] = '#';
    	}else {
    		$arr['news_download'] = $arr['news_download'];
    	}
    	
//     	dump($arr);
//     	exit;
    	//--文章详细页面栏目输出开始
    	$id=$arr['nv_id'];//类别ID
    	$m=D('Column')->order('column_sort ASC')->relation(true)->find($id);
    	 
    	if ($m['f_id']==0){
    		//**获取栏目的下级所有子栏目
    		$m=D('Column')->order('column_sort ASC')->relation(true)->select();
    		$m=Category::getChilds($m,$id);//获取id所有的下级栏目的信息
    	}else {
    		 
    		//**获取栏目的上级所有子栏目
    		$m=D('Column')->order('column_sort ASC')->relation(true)->select();
    		//$m=Category::getChilds($m,$id);//获取id所有的下级栏目的信息
    		$m=Category::getParents($m,$id);//获取id所有的下级栏目的信息
    		 
    		//**获取栏目父级id,并且查询下级所有子栏目
    		$cid=$m['0']['id'];
    		$m=D('Column')->order('column_sort ASC')->relation(true)->select();
    		$m=Category::getChilds($m,$cid);//获取id所有的下级栏目的信息
    	}
    	 
    	// 		dump($m);
    	// 		exit;
    	//二级导航栏目的url，可根据手机站或pc站自动适配url
    	$modlu=__ACTION__ ;
    	strpos($modlu, "mobile");
    	if (strpos($modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写）
    		//栏目url
    		foreach($m as $k3 => $v3){
    			$m[$k3]['url'] = __APP__.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id'];
    		}
    	}else {
    		//栏目url
    		foreach($m as $k3 => $v3){
    			$m[$k3]['url'] = __APP__.'/'.'mobile'.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id'];
    		}
    	}
    	$this->assign('navlist',$m);
    	//--文章详细页面栏目输出结束
    	
    	
    	if (!$arr['news_title']){
    		$this->error('参数错误');
    	}
    	$this->assign('v',$arr);
    	if ($arr['news_dell']==1){
    		$this->error('该文章已经被删除');
    	}
//     	dump($arr);
//     	exit;
    	
    	//获取当前栏目的名称
    	$column_name=$arr['column_name'];
    	$this->assign('vcolumn',$column_name);
//     	dump($column_name);
//     	exit;
    	if ($arr==""){
    		$this->error('参数错误');
    	}
    	//**查询与关键字相似的文章
    	$keyword=$arr['news_keywords'];
    	//dump ($keyword);
    	//exit;
    	//$iarr=explode("，",$keyword);
    	$iarr = preg_split('/[\s,，\/]+/', $keyword);
    	//dump($iarr);
    	$a = $iarr[0];
    	$b = $iarr[1];
    	$c = $iarr[2];
    	$d = $iarr[3];
    	//dump($keyword);
    	//dump($a);
    	//dump($b);
    	//dump($c);
    	//dump($d);
    	//exit;
    	$m=D('News');
    	$data['news_title']=array('like',array("%$a%","%$b%"),'OR');
    	//$i=$m->where($data)->limit('0,4')->order('news_hits desc')->relation(true)->select();
    	$field='g.id,g.news_title,g.news_addtime,g.news_description,i.column_type,r.model_table';
    	$i=$m->alias('g')->join('LEFT JOIN tuzi_column i ON i.id = g.nv_id')->join('LEFT JOIN tuzi_model r ON r.id = i.column_type')->field($field)->order('news_hits desc')->where('news_dell=0')->where($data)->limit('0,4')->select();

    	
    	//二级导航栏目的url，可根据手机站或pc站自动适配url
    	$modlu=__ACTION__ ;
    	strpos($modlu, "mobile");
    	if (strpos($modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写）
    		//文章url
    		foreach($i as $k3 => $v3){
    			$i[$k3]['url'] = __APP__.'/'.$v3['model_table'].'/'.detail.'/'.'id'.'/'.$v3['id'];
    		}
    	}else {
    		//文章url
    		foreach($i as $k3 => $v3){
    			$i[$k3]['url'] = __APP__.'/'.'mobile'.'/'.$v3['model_table'].'/'.detail.'/'.'id'.'/'.$v3['id'];
    		}
    	}	
     	foreach($i as $k3 => $v3){
     		$i[$k3]['news_title'] = $this->substr_ext($v3['news_title'], 0, 20, 'utf-8',"");
     	}
//      	dump($i);
//      	exit;
    	$this->assign('vrelated',$i);
    	
    	//**文章所在的栏目及其上级的栏目
    	import('Class.Category',APP_PATH);//文件在当前项目目录下的class目录
    	$m=D('Column')->order('column_sort ASC')->relation(true)->select();
    	$m=Category::getParents($m,$arr['nv_id']);//获取nv_id所有的上级栏目的信息
    	//$arr['nv_id']表示第一个$arr的select()查询出来的nv_id的具体的值
    	
    	//全局导航栏目的url，可根据手机站或者pc站石洞适配url
    	$modlu=__ACTION__ ;
		strpos($modlu, "mobile");
		if (strpos($modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写）
			//栏目url
	    	foreach($m as $k3 => $v3){
	    		$m[$k3]['url'] = __APP__.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id'];
	    	}
		}else {
			 //栏目url
	    	foreach($m as $k3 => $v3){
	    		$m[$k3]['url'] = __APP__.'/'.'mobile'.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id'];
	    	}
		}
    	
//     	var_dump($m);
//     	exit;
    	$this->assign('topnavlist',$m);

    	//****SEO信息
    	$title=$arr['news_title'];
		$title = substr($title,0,87);//标题过长的情况只要一部分
    	//dump($title);
    	//exit;
    	$Cate_one=$m[0]['column_name'];
    	$Cate_two=$m[1]['column_name'];
    	$Cate_three=$m[2]['column_name'];
    	
    	$mi=M('Config');
    	$data=$mi->field('config_webname')->find();
    	//dump($data);
    	//exit;
    	$title=$title.' - '.$Cate_one.' - '.$data['config_webname'];
    	$Cate_one=$m[0]['column_name'];
    	$Cate_two=$m[1]['column_name'];
    	$Cate_three=$m[2]['column_name'];
    	$keywords=$arr['news_keywords'].','.$Cate_two;
    	$description=$arr['news_description'];
    	//dump($description);
    	//exit;
    	
    	$this->assign('title',$title);
    	$this->assign('keywords',$keywords);
    	$this->assign('description',$description);
    	//dump($Cate);
    	//exit;
    	
		//**全局导航 判断设置最后一个没有尖括号
		$acion_name=ACTION_NAME;//当前模块的方法名称
		$this->assign('acion_name',$acion_name);
		$last=count($m)-1;
		$this->assign('last',$last);
		//echo $last;
		//exit;
    	
    	//**点击数递增
    	D('News')->where(array('id'=>$id))->setInc('news_hits');
    	$this->display();
    }
    
    
    /**
     * 显示友情链接
     */
    public function link(){
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
    public function clicknum(){
    	C('SHOW_PAGE_TRACE','');//关闭当前模块方法的页面trace,这样点击次数js输出没有错。
    	//echo '111';
    	//exit;
    	$id=I('get.id');
		if (!preg_match('/^\d+$/i', $id)){
				$this->error('url参数错误');
				exit;//仿制用户恶意输出url参数
			}
    	//echo $id;
    	//**点击数递增
    	$news_hits=M('News')->where(array('id'=>$id))->getField('news_hits');
    	D('News')->where(array('id'=>$id))->setInc('news_hits');
    
    	echo 'document.write(' . $news_hits . ')';
    }
    
    
    /**
     * 自动登录后，js验证，更新积分
     */
    public function loginChk() {
//     	echo 1112;
//     	exit;
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

    
    
}
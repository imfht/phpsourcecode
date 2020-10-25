<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
/**
 * 自定义标签库
 */
namespace Think\Template\TagLib;
use Think\Template\TagLib;
use Common\Lib\String; //引入类函数
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
class Tuzi extends TagLib{
    // 标签定义
    protected $tags=array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'code'=>array('attr'=>'width,height','close'=>0), //闭合标签      验证码
    	'navlist'=>array('attr'=>'limit,order','close'=>1), //不闭合标签        页头导航条
    	'linklist'=>array('attr'=>'limit,order','close'=>1), //不闭合标签      页尾友情链接
    	'configlist'=>array('attr'=>'limit,order','close'=>1), //不闭合标签     网站配置信息标签
    	'newslist'=>array('attr'=>'nvid,limit,type,order','close'=>1), //不闭合标签     网站配置信息标签
    	
    	'noticelist'=>array('attr'=>'limit,order','close'=>1), //不闭合标签      公告标签
    	'adlist'=>array('attr'=>'navid,limit,order','close'=>1), //不闭合标签      公告标签
    	'fulist'=>array('attr'=>'limit,order','close'=>1), //不闭合标签      公告标签
    	'articlelist'=>array('attr'=>'nvid,limit,type,order','close'=>1), //不闭合标签     网站配置信息标签
        );
    
    /**
     * 浮动客服标签
     */
    public function _fulist($attr, $content) {
    	//$attr = $this->parseXmlAttr($attr);
    	 
    	$limit = empty($attr['limit'])? '0,3' : $attr['limit'];
    	$order = empty($attr['order'])? 'id' : $attr['order'];
//     	    	dump($limit);
//     	    	exit;
    	 
    	$str = <<<str
<?php
    
	\$_link_m=D('Kefu')->order("$order")->limit("$limit")->select();
    
// 	dump(\$_link_m);
// 	exit;
    
	foreach(\$_link_m as \$_link_v):
			extract(\$_link_v);
// 		dump(\$_link_m);
// 		exit;
?>
str;
    
    	$str .= $content;
        	$str .='<?php endforeach;?>';
    	return $str;
    }
    
    /**
     * 公告标签
     */
    public function _adlist($attr, $content) {
    	//$attr = $this->parseXmlAttr($attr);
    	
    	$navid = empty($attr['navid'])? '0' : $attr['navid']; //广告分类id
    	$limit = empty($attr['limit'])? '0,3' : $attr['limit']; //查询数目
    	$order = empty($attr['order'])? 'advert_sort' : $attr['order']; //排序字段
    	$by = empty($attr['by'])? '' : $attr['by']; //排序方法
//     	    	dump($order);
//     	    	exit;
    	 
    	$str = <<<str
<?php
		//查询指定id的栏目信息
		\$id=$navid;
		\$topnav=M('Adnav')->where("id=\$id")->select();
// 		dump(\$topnav);
// 		exit;
		 
		//查询指定id的栏目下的所有文章
		foreach (\$topnav as \$k => \$v){
			//查询数据，没有分页
			\$where['advert_nav'] = \$id;
			\$topnav[\$k]['news']=D('Advert')->where(\$where)->where('advert_show=0')->limit("$limit")->order("$order $by")->relation(true)->select();
			\$_result_v=\$topnav[\$k]['news'];
		}
		//循环截取字符 substr_ext函数写在commonaction.class.php中
		foreach(\$_result_v as \$k2 => \$v2){
			\$_result_v[\$k2]['advert_name'] = Common\Lib\Common::substr_ext(\$v2['advert_name'], 0, 16, 'utf-8',"");
		}
					
		foreach(\$_result_v as \$k2 => \$v2){
			\$_result_v[\$k2]['advert_image'] = '__ROOT__'.'/'.'Uploads'.\$v2['advert_image'];
		}
// 		dump(\$_result_v);
// 		exit;
    
	foreach(\$_result_v as \$_result_m):
			extract(\$_result_m);
// 		dump(\$_result_m);
// 		exit;
?>
str;
    
    	$str .= $content;
        	$str .='<?php endforeach;?>';
    	return $str;
    }
    
    /**
     * 公告标签
     */
    public function _noticelist($attr, $content) {
    	//$attr = $this->parseXmlAttr($attr);
    	
    	$limit = empty($attr['limit'])? '0,3' : $attr['limit'];
    	$order = empty($attr['order'])? 'id desc' : $attr['order'];
//     	dump($limit);
//     	exit;
    	
    	$str = <<<str
<?php
    
	\$_link_m=D('Notice')->order("$order")->limit("$limit")->select();	
    	//循环截取标题  substr_ext函数写在commonaction.class.php中
		foreach(\$_link_m as \$k2 => \$v2){
			\$_link_m[\$k2]['notice_title'] = Common\Lib\Common::substr_ext(\$v2['notice_title'], 0, 22, 'utf-8',"");
		}
// 	dump(\$_link_m);
// 	exit;
    
	foreach(\$_link_m as \$_link_v):
			extract(\$_link_v);
// 		dump(\$_link_m);
// 		exit;
?>
str;
    
    	$str .= $content;
    	$str .='<?php endforeach;?>';
    	return $str;
    }
    
    /**
     * 指定新闻列表标签
     */
    public function _newslist($attr, $content) {
    	$nvid = empty($attr['nvid'])? 'mull' : $attr['nvid']; //查询栏目
    	$limit = empty($attr['limit'])? '0,10' : $attr['limit']; //查询个数
    	$order = empty($attr['order'])? 'news_sort' : $attr['order']; //排序字段
    	$by = empty($attr['by'])? '' : $attr['by']; //排序方法
    	$type = empty($attr['type'])? 'mull' : $attr['type']; //鉴定属性类别ID
//     	dump($nvid);
//     	exit;
    	
    	$str = <<<str
<?php
    	
		//**开始 查询指定id的所有子id
    	\$id=$nvid;
    	\$topcate=M('Column')->where("id=\$id")->order('column_sort')->select();
//     	dump(\$topcate);
//     	exit;
    	
    	//查询所有栏目的信息
    	\$m=M('Column')->order('column_sort')->select();
//     	dump(\$m);
//     	exit;
    	
    	//查询指定id的栏目下的所有文章
    	foreach (\$topcate as \$k => \$v4){
    		\$cids=Common\Lib\Category::getChildsId(\$m, \$v4['id']);
    		\$cids[]=\$v4['id'];
//     		dump(\$cids);
//     		exit;
		}
		//**结束 查询指定id的所有子id
    		
    	//查询指定id的栏目信息
    	\$id=$type; 
    	\$m=D('Attr');
    	
    	\$data['g.id']= \$id;
    	\$where=array('nv_id'=>array('IN', \$cids));
    	
    	\$field='g.id,g.attr_name,g.attr_color,i.news_id,i.attr_id,r.id,r.nv_id,r.news_title,r.news_content,r.news_hits,r.news_author,r.news_addtime,r.news_updatetime,r.news_sort,r.news_pic,f.column_name,m.model_table';
    	\$result=\$m->alias('g')->join('LEFT JOIN tuzi_attr_news i ON i.attr_id = g.id')->join('LEFT JOIN tuzi_news r ON r.id = i.news_id')->join('LEFT JOIN tuzi_column f ON f.id = r.nv_id')->join('LEFT JOIN tuzi_model m ON m.id = f.column_type')->field(\$field)->limit("$limit")->order('r.$order $by')->where(\$data)->where(\$where)->where("news_dell=0")->select();
// 		    	dump(\$result);
// 		    	exit;
    	foreach(\$result as \$k2 => \$v2){
    		\$result[\$k2]['news_title'] = Common\Lib\Common::substr_ext(\$v2['news_title'], 0, 25, 'utf-8',"");
    	}	
    	foreach(\$result as \$k2 => \$v2){
    		\$result[\$k2]['news_content'] = Common\Lib\Common::substr_ext(\$v2['news_content'], 0, 80, 'utf-8',"");
    	}
    				
		foreach(\$result as \$k2 => \$v2){
    			\$pic=\$v2['news_pic'];
    			strpos(\$pic, "nopic");
				if (strpos(\$pic, "nopic")==''){//如果url中不存在data（不区分大小写
    				\$result[\$k2]['news_pic'] = '__ROOT__'.\$v2['news_pic'];
    			}else{
    				\$result[\$k2]['news_pic'] = '__ROOT__'."/Data/Images/nopic.jpg";
    			}
		}


		//文章的url，可根据手机站或pc站自动适配url
		\$modlu='__ACTION__';
		strpos(\$modlu, "mobile");
		if (strpos(\$modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写
    		//电脑站url
			foreach(\$result as \$k3 => \$v3){
     		\$result[\$k3]['url'] = '__APP__'.'/'.\$v3['model_table'].'/'.detail.'/'.'id'.'/'.\$v3['id'];
     		}
		}else {
    		//手机站url
			foreach(\$result as \$k3 => \$v3){
     		\$result[\$k3]['url'] = '__APP__'.'/'.'mobile'.'/'.\$v3['model_table'].'/'.detail.'/'.'id'.'/'.\$v3['id'];
     		}
		}
    				
    	foreach(\$result as \$result_v):
			extract(\$result_v);
    		
//     	dump(\$result);
// 		exit;
?>
str;
    
    	$str .= $content;
    	$str .='<?php endforeach;?>';
    	return $str;
    }
    
    /**
     * 验证码标签
     */
    public function _code($attr)   {//闭合标签不需要$content
    	$width = empty($attr['width'])? '200' : $attr['width'];
    	$height = empty($attr['height'])? '32' : $attr['height'];
//     	dump($height);
//     	exit;
    	$str="<img src='__APP__/Manage/Verify/code?w={$width}&h={$height}' onclick='this.src=this.src+\"?\"+Math.randon'/>";
//     	//实现验证码功能的代码，最后return出去
    	return $str;
    }
    
    /**
     * 导航条标签
     */
	public function _navlist($attr, $content) {

// 		$width = empty($attr['width'])? '200' : $attr['width'];
// 		$height = empty($attr['height'])? '32' : $attr['height'];
		//$attr = $this->parseXmlAttr($attr);
		
		$str = <<<str
		
<?php
	\$_nav_m=D('Column')->order("{$attr['order']}")->field('id,f_id,column_name,column_ename,column_url,column_type,column_sort,column_status,column_link')->where("column_status=0")->relation(true)->select();
	\$_nav_m=Common\Lib\Category::unlimitedForLayer(\$_nav_m);
	// 	dump(\$_nav_m);
	// 	exit;
			
	 //栏目的url，可根据手机站或pc站自动适配url
     	\$modlu='__ACTION__';
// 	 	dump(\$modlu);
// 		exit;
		strpos(\$modlu, "mobile");
		if (strpos(\$modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写）
			//栏目url
			foreach(\$_nav_m as \$k3 => \$v3){
				if(\$v3['column_link']==1){
					\$_nav_m[\$k3]['url'] = '__APP__'.'/'.\$v3['column_url'];
				}
			
				if(\$v3['column_link']==2){
					\$_nav_m[\$k3]['url'] = '__APP__'.'/'.\$v3['column_ename'];
				}
			
				if(\$v3['column_link']==0){
					\$_nav_m[\$k3]['url'] = '__APP__'.'/'.\$v3['url'].'/'.group.'/'.'id'.'/'.\$v3['id'];
				}
     		}
		}else {
			//栏目url
			foreach(\$_nav_m as \$k3 => \$v3){
				if(\$v3['column_link']==1){
					\$_nav_m[\$k3]['url'] = '__APP__'.'/'.'mobile'.'/'.\$v3['column_url'];
				}
				
				if(\$v3['column_link']==2){
					\$_nav_m[\$k3]['url'] = '__APP__'.'/'.'mobile'.'/'.\$v3['column_ename'];
				}
			
				if(\$v3['column_link']==0){
					\$_nav_m[\$k3]['url'] = '__APP__'.'/'.'mobile'.'/'.\$v3['url'].'/'.group.'/'.'id'.'/'.\$v3['id'];
				}		
     		}

		}
	
// 	dump(\$_nav_m);
// 	exit;
	
	foreach(\$_nav_m as \$autoindex => \$_nav_v):
	extract(\$_nav_v);
	
			
// 	dump(\$_nav_m);
// 	exit;
?>
str;

	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}
	
	
	/**
	 * 友情链接标签
	 */
	public function _linklist($attr, $content) {
		$order = empty($attr['order'])? '200' : $attr['order'];
		$by = empty($attr['by'])? '' : $attr['by']; //排序方法
		$limit = empty($attr['limit'])? '32' : $attr['limit'];
// 		    	dump($order);
// 		    	exit;
		//$attr = $this->parseXmlAttr($attr);
		$str = <<<str
<?php
	
	\$_link_m=D('Link')->order('$order $by')->limit("$limit")->where("link_show=0")->select();
// 	dump(\$_link_m);
// 	exit;
	
	foreach(\$_link_m as \$_link_v):
			extract(\$_link_v);
// 		dump(\$_link_m);
// 		exit;
?>
str;
	
		$str .= $content;
		$str .='<?php endforeach;?>';
		return $str;
	}
    
	/**
	 * 网站配置信息标签
	 */
	public function _configlist($attr, $content) {
		//$attr = $this->parseXmlAttr($attr);
		$str = <<<str
<?php
	
	\$_config_m=D('Config')->select();
// 	dump(\$_config_m);
// 	exit;
	
	foreach(\$_config_m as \$_config_v):
			extract(\$_config_v);
?>
str;
	
		$str .= $content;
		$str .='<?php endforeach;?>';
		return $str;
	}
	
	/**
	 * 指定新闻列表标签
	 */
	public function _articlelist($attr, $content) {
		$nvid = empty($attr['nvid'])? 'mull' : $attr['nvid']; //查询栏目
		$limit = empty($attr['limit'])? '0,10' : $attr['limit']; //查询个数
		$order = empty($attr['order'])? 'news_sort' : $attr['order']; //排序字段
		$by = empty($attr['by'])? '' : $attr['by']; //排序方法
		$type = empty($attr['type'])? 'mull' : $attr['type']; //鉴定属性类别ID
		//     	dump($nvid);
		//     	exit;
		 
		$str = <<<str
<?php
   
		//**开始 查询指定id的所有子id
    	\$id=$nvid;
    	\$topcate=M('Column')->where("id=\$id")->order('column_sort')->select();
//     	dump(\$topcate);
//     	exit;
   
    	//查询所有栏目的信息
    	\$m=M('Column')->order('column_sort')->select();
//     	dump(\$m);
//     	exit;
   
    	//查询指定id的栏目下的所有文章
    	foreach (\$topcate as \$k => \$v4){
    		\$cids=Common\Lib\Category::getChildsId(\$m, \$v4['id']);
    		\$cids[]=\$v4['id'];
//     		dump(\$cids);
//     		exit;
		}
		//**结束 查询指定id的所有子id
	
    	//查询指定id的栏目信息
    	\$where=array('nv_id'=>array('IN', \$cids));
			
		\$m=D('News');
		\$field='g.id,g.news_title,g.news_addtime,g.news_description,g.news_pic,g.news_hits,g.news_author,g.news_download,i.column_type,r.model_table';
    	\$result=\$m->alias('g')->join('LEFT JOIN tuzi_column i ON i.id = g.nv_id')->join('LEFT JOIN tuzi_model r ON r.id = i.column_type')->field(\$field)->where(\$where)->where("news_dell=0")->relation(true)->limit("$limit")->order('$order $by')->select();
				
// 		dump(\$result);
// 		exit;
    	foreach(\$result as \$k2 => \$v2){
    		\$result[\$k2]['news_title'] = Common\Lib\Common::substr_ext(\$v2['news_title'], 0, 25, 'utf-8',"");
    	}
    	foreach(\$result as \$k2 => \$v2){
    		\$result[\$k2]['news_content'] = Common\Lib\Common::substr_ext(\$v2['news_content'], 0, 80, 'utf-8',"");
    	}
	
		foreach(\$result as \$k2 => \$v2){
    			\$pic=\$v2['news_pic'];
    			strpos(\$pic, "nopic");
				if (strpos(\$pic, "nopic")==''){//如果url中不存在data（不区分大小写
    				\$result[\$k2]['news_pic'] = '__ROOT__'.\$v2['news_pic'];
    			}else{
    				\$result[\$k2]['news_pic'] = '__ROOT__'."/Data/Images/nopic.jpg";
    			}
		}
	
	
		//文章的url，可根据手机站或pc站自动适配url
		\$modlu='__ACTION__';
		strpos(\$modlu, "mobile");
		if (strpos(\$modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写
    		//电脑站url
			foreach(\$result as \$k3 => \$v3){
     		\$result[\$k3]['url'] = '__APP__'.'/'.\$v3['model_table'].'/'.detail.'/'.'id'.'/'.\$v3['id'];
     		}
		}else {
    		//手机站url
			foreach(\$result as \$k3 => \$v3){
     		\$result[\$k3]['url'] = '__APP__'.'/'.'mobile'.'/'.\$v3['model_table'].'/'.detail.'/'.'id'.'/'.\$v3['id'];
     		}
		}
	
    	foreach(\$result as \$result_v):
			extract(\$result_v);
	
//     	dump(\$result);
// 		exit;
?>
str;
	
    	$str .= $content;
    	$str .='<?php endforeach;?>';
	    	return $str;
    }

   
}

?>


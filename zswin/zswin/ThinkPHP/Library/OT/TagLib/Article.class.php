<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace OT\TagLib;
use Think\Template\TagLib;

class Article extends TagLib{
	/**
	 * 定义标签列表
	 * @var array
	 */
	protected $tags   =  array(
		
		'prev'     => array('attr' => 'name,sign,info', 'close' => 1), //获取上一篇文章信息
		'next'     => array('attr' => 'name,sign,info', 'close' => 1), //获取下一篇文章信息
		'page'     => array('attr' => 'cate,child,uid,status,row,title,onetag,position,focus', 'close' => 0), //列表分页
		'list'     => array('attr' => 'name,cate,child,uid,page,row,field,status,order,title,onetag,position,limit,focus', 'close' => 1), //获取指定分类列表
	);

	public function _list($tag, $content){
		$name   = $tag['name'];
		$position = empty($tag['position']) ? '' : $tag['position'];
		$cate = empty($tag['cate']) ? 0 : $tag['cate'];//0表示所有分类
		$uid = empty($tag['uid']) ? 0 : $tag['uid'];//0表示所有用户
		$onetag = empty($tag['onetag']) ? '' : $tag['onetag'];
		$title = empty($tag['title']) ? '' : $tag['title'];
		$status = empty($tag['status']) ? 1 : $tag['status'];//0待审核1审核通过5草稿
		
		if($cate==0){
		   $tag['child']  = 'true';//如果分类id为0或者不填写，则代表取出所有分类，包括子分类	
		}
		$child  = empty($tag['child']) ? 'true' : $tag['child'];
		
		$row    = empty($tag['row'])   ? '10' : $tag['row'];
		$field  = empty($tag['field']) ? '' : $tag['field'];
        $order  = empty($tag['order']) ? 'tj desc,id desc' : $tag['order'];
		 $limit  = empty($tag['limit']) ? false : $tag['limit'];
		 $focus  = empty($tag['focus']) ? false : $tag['focus'];
        $map=strapiarr($cate,$child,$uid,$status,$order,$field,$row,$onetag,$title,$position,$limit,$focus);
       

        
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Art/getArt",$map);$__LIST__ = $data[\'data\'];';
		$parse .= ' ?>';
		$parse .= '<volist name="__LIST__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse;
	}
    /* 列表数据分页 */
	public function _page($tag){
		$row = $tag['row'];
		$cate = empty($tag['cate']) ? 0 : $tag['cate'];//0表示所有分类
		$uid = empty($tag['uid']) ? 0 : $tag['uid'];//0表示所有用户
		$onetag = empty($tag['onetag']) ? '' : $tag['onetag'];
		$title = empty($tag['title']) ? '' : $tag['title'];
		$status = empty($tag['status']) ? 1 : $tag['status'];//0待审核1审核通过5草稿
		$position = empty($tag['position']) ? '' : $tag['position'];
		 $focus  = empty($tag['focus']) ? false : $tag['focus'];
		if($cate==0){
		   $tag['child']  = 'true';//如果分类id为0或者不填写，则代表取出所有分类，包括子分类	
		}
		$child  = empty($tag['child']) ? 'true' : $tag['child'];
		
		$map=strapiarr($cate,$child,$uid,$status,$onetag,$title,$position,$focus);
        $parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Art/getArtCount",$map);$count = $data[\'data\'];';
		$parse  .= '$__PAGE__ = new \Think\Page($count, ' . $row . ');';
		$parse  .= 'echo $__PAGE__->show();';
		$parse  .= ' ?>';
		return $parse;
	}

	/* 获取下一篇文章信息 */
	public function _next($tag, $content){
		$name   = $tag['name'];
		
		$info   = $tag['info'];//根据info内容得到上下文,支持按作者，分类和标签
		$sign = empty($tag['sign']) ? 'cate' : $tag['sign'];//作者user，分类cate和标签tag
		$map=strapiarr($info,$sign);
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Art/getNextArt",$map);';
		$parse .= '$' . $name . ' = $data[\'data\'];';
		$parse .= ' ?>';
		$parse .= '<notempty name="' . $name . '">';
		$parse .= $content;
		$parse .= '</notempty>';
		return $parse;
	}

	/* 获取上一篇文章信息 */
	public function _prev($tag, $content){
		$name   = $tag['name'];
		
		$sign = empty($tag['sign']) ? 'cate' : $tag['sign'];//作者user，分类cate和标签tag
		$info   = $tag['info'];//根据info内容得到上下文,支持按作者，分类和标签
		
		$map=strapiarr($info,$sign);
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Art/getPreArt",$map);';
		$parse .= '$' . $name . ' = $data[\'data\'];';
		$parse .= ' ?>';
		$parse .= '<notempty name="' . $name . '">';
		$parse .= $content;
		$parse .= '</notempty>';
		return $parse;
	}

	

	
}
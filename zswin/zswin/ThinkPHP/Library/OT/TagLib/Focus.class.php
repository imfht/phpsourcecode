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

class Focus extends TagLib{
	/**
	 * 定义标签列表
	 * @var array
	 */
	protected $tags   =  array(
		'userpage'     => array('attr' => 'uid,row', 'close' => 0), //列表分页
		'userlist'     => array('attr' => 'name,uid,row,limit', 'close' => 1),
		'tagpage'     => array('attr' => 'uid,row', 'close' => 0), //列表分页
		'taglist'     => array('attr' => 'name,uid,row,limit', 'close' => 1),
		'artpage'     => array('attr' => 'uid,status,row,position', 'close' => 0), //列表分页
		'artlist'     => array('attr' => 'name,uid,row,field,status,order,position,limit', 'close' => 1),
	);
	public function _userlist($tag, $content){
		$name   = $tag['name'];
		
	
		$uid = empty($tag['uid']) ? 1 : $tag['uid'];//0表示所有用户
	    $row    = empty($tag['row'])   ? '10' : $tag['row'];
		
		$limit  = empty($tag['limit']) ? false : $tag['limit'];
		$map=strapiarr($uid,$row,$limit);
		 
	
	
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Focus/getUser",$map);$__LIST__ = $data[\'data\'];';
		$parse .= ' ?>';
		$parse .= '<volist name="__LIST__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse;
	}
	/* 列表数据分页 */
	public function _userpage($tag){
		$row = $tag['row'];
		
		$uid = empty($tag['uid']) ? 1 : $tag['uid'];//0表示所有用户
	
		
	
	
		$map=strapiarr($uid);
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Focus/getUserCount",$map);$count = $data[\'data\'];';
		$parse  .= '$__PAGE__ = new \Think\Page($count, ' . $row . ');';
		$parse  .= 'echo $__PAGE__->show();';
		$parse  .= ' ?>';
		return $parse;
	}
public function _taglist($tag, $content){
		$name   = $tag['name'];
		
	
		$uid = empty($tag['uid']) ? 1 : $tag['uid'];//0表示所有用户
	    $row    = empty($tag['row'])   ? '10' : $tag['row'];
		
		$limit  = empty($tag['limit']) ? false : $tag['limit'];
		$map=strapiarr($uid,$row,$limit);
		 
	
	
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Focus/getTag",$map);$__LIST__ = $data[\'data\'];';
		$parse .= ' ?>';
		$parse .= '<volist name="__LIST__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse;
	}
	/* 列表数据分页 */
	public function _tagpage($tag){
		$row = $tag['row'];
		
		$uid = empty($tag['uid']) ? 1 : $tag['uid'];//0表示所有用户
	
		
	
	
		$map=strapiarr($uid);
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Focus/getTagCount",$map);$count = $data[\'data\'];';
		$parse  .= '$__PAGE__ = new \Think\Page($count, ' . $row . ');';
		$parse  .= 'echo $__PAGE__->show();';
		$parse  .= ' ?>';
		return $parse;
	}
	public function _artlist($tag, $content){
		$name   = $tag['name'];
		$position = empty($tag['position']) ? '' : $tag['position'];
		
		$uid = empty($tag['uid']) ? 1 : $tag['uid'];//0表示所有用户
	
		$status = empty($tag['status']) ? 1 : $tag['status'];//0待审核1审核通过5草稿
		
		
		$row    = empty($tag['row'])   ? '10' : $tag['row'];
		$field  = empty($tag['field']) ? '' : $tag['field'];
        $order  = empty($tag['order']) ? 'tj desc,id desc' : $tag['order'];
		$limit  = empty($tag['limit']) ? false : $tag['limit'];
        $map=strapiarr($uid,$status,$order,$field,$row,$position,$limit);
       

        
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Focus/getArt",$map);$__LIST__ = $data[\'data\'];';
		$parse .= ' ?>';
		$parse .= '<volist name="__LIST__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse;
	}
    /* 列表数据分页 */
	public function _artpage($tag){
		$row = $tag['row'];
		$position = empty($tag['position']) ? '' : $tag['position'];
		$uid = empty($tag['uid']) ? 1 : $tag['uid'];//0表示所有用户
	
		$status = empty($tag['status']) ? 1 : $tag['status'];//0待审核1审核通过5草稿
		
	
		$map=strapiarr($uid,$status,$position);
        $parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Focus/getArtCount",$map);$count = $data[\'data\'];';
		$parse  .= '$__PAGE__ = new \Think\Page($count, ' . $row . ');';
		$parse  .= 'echo $__PAGE__->show();';
		$parse  .= ' ?>';
		return $parse;
	}

	

	
}
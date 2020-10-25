<?php

namespace OT\TagLib;
use Think\Template\TagLib;

class Member extends TagLib{
	/**
	 * 定义标签列表
	 * @var array
	 */
	protected $tags   =  array(
		

		'page'     => array('attr' => 'score', 'close' => 0),
		'list'     => array('attr' => 'name,order,row,field,limit', 'close' => 1),
	);

	public function _list($tag, $content){
		$name   = $tag['name'];

		$row    = empty($tag['row'])   ? '10' : $tag['row'];
		$field  = empty($tag['field']) ? '' : $tag['field'];
        $order  = empty($tag['order']) ? 'uid desc' : $tag['order'];
		 $limit  = empty($tag['limit']) ? false : $tag['limit'];
        $map=strapiarr($order,$field,$row,$limit);
       

        
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Member/getMember",$map);$__LIST__ = $data[\'data\'];';
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
		
		if($cate==0){
		   $tag['child']  = 'true';//如果分类id为0或者不填写，则代表取出所有分类，包括子分类	
		}
		$child  = empty($tag['child']) ? 'true' : $tag['child'];
		
		$map=strapiarr($cate,$child,$uid,$status,$onetag,$title);
        $parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Art/getArtCount",$map);$count = $data[\'data\'];';
		$parse  .= '$__PAGE__ = new \Think\Page($count, ' . $row . ');';
		$parse  .= 'echo $__PAGE__->show();';
		$parse  .= ' ?>';
		return $parse;
	}

	

	

	
}
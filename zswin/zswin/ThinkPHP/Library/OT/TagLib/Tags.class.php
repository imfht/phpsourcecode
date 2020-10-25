<?php

namespace OT\TagLib;
use Think\Template\TagLib;

class Tags extends TagLib{
	/**
	 * 定义标签列表
	 * @var array
	 */
	protected $tags   =  array(
		

		'page'     => array('attr' => 'row,type', 'close' => 0), //列表分页
		'list'     => array('attr' => 'name,order,row,field,limit,type', 'close' => 1),
	);

	public function _list($tag, $content){
		$name   = $tag['name'];

		$row    = empty($tag['row'])   ? '10' : $tag['row'];
		$field  = empty($tag['field']) ? '' : $tag['field'];
        $order  = empty($tag['order']) ? 'uid desc' : $tag['order'];
		$limit  = empty($tag['limit']) ? false : $tag['limit'];
		$type  = empty($tag['type']) ? '1' : $tag['type'];
        $map=strapiarr($order,$field,$row,$limit,$type);
       

        
		$parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Tag/getTags",$map);$__LIST__ = $data[\'data\'];';
		$parse .= ' ?>';
		$parse .= '<volist name="__LIST__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse;
	}
     /* 列表数据分页 */
	public function _page($tag){
		$row    = empty($tag['row'])   ? '10' : $tag['row'];
		$type  = empty($tag['type']) ? '1' : $tag['type'];
		
		$map=strapiarr($type);
        $parse  = '<?php ';
		$parse  .= '$map='.$map.';';
		$parse .= '$data = callApi("Tag/getTagsCount",$map);$count = $data[\'data\'];';
		$parse  .= '$__PAGE__ = new \Think\Page($count, ' . $row . ');';
		$parse  .= 'echo $__PAGE__->show();';
		$parse  .= ' ?>';
		return $parse;
	}

	

	

	
}
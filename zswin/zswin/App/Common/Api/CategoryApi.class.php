<?php
namespace Common\Api;
class CategoryApi {
	public $cateListAll=array();
	
	 protected  function cateChildList($pid,$nb,$type=1)
    {
    	
    $cate=M('cate');
    $map['pid']=$pid;
  
	$map ['type'] = $type;				
	
	
    $parent=$cate->where($map)->order('ordid asc,id desc')->select();
    if($parent)
    {
        $nb = $nb."&nbsp;";
        foreach($parent as $item)
        {
            $item['name']=$nb.'├ '.$item['name'];
            
            $this->cateListAll[]=$item;
            $this->cateChildList($item['id'],$nb);
        }
    }
    
    }
 
     /**
     * 获取某类型下所有分类并缓存分类
     * @param  integer $pid    从哪一级开始，根节点是0
     * @param  string  $field 类型ID
     * @return string         分类信息
     */
    public  function get_catelist($pid,$type = 1){
    	
    	    $this->cateChildList($pid, $nb,$type);
    	    $res=$this->cateListAll;
    		S("sys_catelist_".$type, $res); //更新缓存
    		
    	
    	
    	return $res;
    }
    /**
     * 获取分类信息并缓存分类
     * @param  integer $id    分类ID
     * @param  string  $field 要获取的字段名
     * @return string         分类信息
     */
    public static function get_cate($id, $field = null){
        static $list;

        /* 非法分类ID */
        if(empty($id) || !is_numeric($id)){
            return '';
        }

        /* 读取缓存数据 */
        if(empty($list)){
            $list = S('sys_cate_list');
        }

        /* 获取分类名称 */
        if(!isset($list[$id])){
            $cate = M('Cate')->find($id);
            if(!$cate || 1 != $cate['status']){ //不存在分类，或分类被禁用
                return '';
            }
            $list[$id] = $cate;
            S('sys_cate_list', $list); //更新缓存
        }
        return is_null($field) ? $list[$id] : $list[$id][$field];
    }

    /* 根据ID获取分类标识 */
    public static function get_cate_name($id){
        return get_cate($id, 'name');
    }
    /* 获得会员可发布内容的分类总数，接受类型id */
    public static function get_editcnum($type=1){
    	
       $map['type']=$type;
       if(!is_admin(is_login())){
       	$map['enable']=1;	
       }
       
    	
       $num = M('cate')->where($map)->count();
       return $num;
    }
    
}
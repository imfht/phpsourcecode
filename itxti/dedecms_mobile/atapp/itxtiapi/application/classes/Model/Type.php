<?php
defined('SYSPATH') or die('No direct script access.');

class Model_Type extends Model_Database {
	//获取栏目列表
	public function get_typelist($typeid) {
        $query = DB::select('id','reid','typename')->from('arctype');
        !empty($typeid) &&  $query->where('reid','=',$typeid);
        $query->where('ishidden','=',0);
        $data = $query->execute()->as_array();
        return $data;
    }

	//获取指定栏目信息
	public function get_typeinfo($typeid) {
        $data = DB::select()->from('arctype')->where('id','=',$typeid)
        		->and_where('ishidden','=',0)
        		->execute()->current();
        return $data;
    }



}

?>
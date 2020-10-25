<?php
defined('SYSPATH') or die('No direct script access.');

class Model_Article extends Model_Database {
	//获取栏目列表
	public function get_arclist($typeid,$pagesize = 10,$startid = 0,$getall,$order) {
        $query = DB::select()->from(array('archives','a'));
        if($getall){
            //
            !empty($typeid) &&  $query->where('typeid','IN',$typeids);
        }else{
            !empty($typeid) &&  $query->where('typeid','=',$typeid);
        }
        $query->where('arcrank','=',0)->limit($pagesize)->offset($startid);
        !empty($order) &&  $query->order_by($order);
        $data = $query->execute()->as_array();
        return $data;
    }


    //获取文章内容
    public function get_article($aid) {
        $query = DB::select()->from(array('archives','a'))
        ->join(array('addonarticle','b'),'LEFT')->on('a.id','=','b.aid');
        $query->where('a.arcrank','=',0)->and_where('a.id','=',$aid);
        $data = $query->execute()->current();
        return $data;
    }




}

?>
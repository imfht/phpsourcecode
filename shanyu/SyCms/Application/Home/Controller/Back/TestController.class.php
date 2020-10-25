<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
    public function index(){
    	M()->startTrans();
    	echo M()->_sql();
    	echo '<br/>';
    	$goods=M('Goods')->lock(true)->find(2);
    	echo M()->_sql();
    	echo '<br/>';
    	$data=array(
    		'name'=>'苹果',
    		'price'=>3,
    		'number'=>100,
    	);
    	M('Goods')->add($data);
    	echo M('Goods')->getLastSql();
    	echo '<br/>';
    	if($goods)
        {
            $result = true;
            if(!$result)
            {
                M()->rollback();
                echo M()->_sql();
                echo '<br/>';
            }
        }
        M()->commit();
        echo M()->_sql();
        echo '<br/>';
    }
}
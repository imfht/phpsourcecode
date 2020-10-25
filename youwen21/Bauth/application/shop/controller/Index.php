<?php

namespace app\shop\controller;

use youwen\think_goods\Goods;

class Index
{
    public function index()
    {
    	$good = new Goods;

    	echo '<pre>';
    	print_r( 'xx' );
    	exit('</pre>');
        return \think\View::instance()->fetch();
    }

    public function goodRow()
    {
    	$good = new Goods();
    	$data = $good->goodsRow(1);
    	echo '<pre>';
    	print_r( $data );
    	exit('</pre>');
    }

    public function lists()
    {
    	$good = new Goods();
    	$list = $good->goodsList();
    	echo '<pre>';
    	print_r( $list );
    	exit('</pre>');
    }
}

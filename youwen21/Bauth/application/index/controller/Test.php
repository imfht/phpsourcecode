<?php
namespace app\index\controller;
use youwen\think_region\Region;


class Test
{
    public function index()
    {
        $list = Region::provinceList();
        // $list = Ttt::lists();
        echo '<pre>';
        print_r( $list );
        exit('</pre>');
    }

    public function city()
    {
        
        $list = Region::cityList(130000);
        echo '<pre>';
        print_r( $list );
        exit('</pre>');
    }

    public function county()
    {
        
        $list = Region::countyList(130700);
        echo '<pre>';
        print_r( $list );
        exit('</pre>');
    }

    public function address()
    {
        $ret = Region::getAddress(130000, 130700, 130730);
        echo '<pre>';
        print_r( $ret );
        exit('</pre>');
    }

    public function detail()
    {
        $ret = Region::getDetailByCode('130700');
        // $ret = Region::getDetailByName('张家口市');
        echo '<pre>';
        print_r( $ret );
        exit('</pre>');
    }
    
}

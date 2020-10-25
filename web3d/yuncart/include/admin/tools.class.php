<?php

defined('IN_CART') or die;

/**
 *
 * 工具箱 
 *
 */
class Tools extends Base
{

    /**
     *  
     * 工具箱首页
     *
     *
     * */
    public function index()
    {

        $data = array();
        output("tools_index", $data);
    }

}

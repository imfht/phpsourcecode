<?php

defined('IN_CART') or die;

/**
 *  
 * 友情连接
 *
 *
 * */
class Link extends Base
{

    public function __construct($model, $action)
    {
        parent::__construct($model, $action);
    }

    /**
     *  
     * 友链
     *
     *
     * */
    public function index()
    {
        $this->data["links"] = DB::getDB()->select("link", "*");

        $this->output("link_index");
    }

}

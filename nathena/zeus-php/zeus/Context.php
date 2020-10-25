<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 15/11/9
 * Time: 11:07
 */

namespace zeus;


abstract class Context
{

    protected $_viewArgs = array();
    protected $_tpl = "";

    protected $current_url;

    public function __construct()
    {
        $this->current_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }

    protected function viewArg($key,$val)
    {
        $this->_viewArgs[$key] = $val;
    }

    protected function render($tpl)
    {
        $this->_tpl = $tpl;

        extract($this->_viewArgs);

        include tpl($this->_tpl);
    }
}
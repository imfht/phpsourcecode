<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class Share extends M_Controller {

    private $share;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $MOD = $this->get_module(SITE_ID);
        if ($MOD) {
            foreach ($MOD as $c) {
                $c['share'] && $this->share[$c['dirname']] = $c;
            }
        }
    }

    // index
    public function index() {

    }

    // 内容管理
    public function content() {

    }

}
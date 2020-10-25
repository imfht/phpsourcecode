<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mall extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['active'] = 'mall';
    }

    public function index($type = 'latest', $page_index = 1, $page_size = 50)
    {
        echo 'in coding ε=ε=(ノ≧∇≦)ノ';
    }
}

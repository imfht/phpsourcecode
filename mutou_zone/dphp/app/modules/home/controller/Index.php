<?php
namespace home\controller;

use thinker\Controller;

class Index extends Controller
{

    public function view()
    {
        $this->view->display("home\index");
    }
}
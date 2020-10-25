<?php
namespace module\admin;

use \lib\Action, \lib\RBAC;

class indexMod extends base
{

    public function index()
    {
        $this->redirect(url('meeting/index'));
    }
}
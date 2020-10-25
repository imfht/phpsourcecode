<?php
namespace app\index\controller;

use think\Controller;
use think\File;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}

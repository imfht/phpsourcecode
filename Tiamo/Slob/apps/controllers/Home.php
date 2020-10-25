<?php
namespace App\Controller;
use App\BasicController;
use Swoole;

class Home extends BasicController
{
    function index()
    {
		$title="后台主页";
		$this->assign("title", $title);
        $this->display("home/index.php");
    }
}
<?php
namespace App\Controller;
use Swoole;

class Session extends Swoole\Controller
{
    function write()
    {
        //使用此函数代替PHP的session_start
        $this->session->start();
        $_SESSION['test'] = 1;
    }

    function read()
    {
        //使用此函数代替PHP的session_start
        $this->session->start();
        echo $_SESSION['test'];
    }
}

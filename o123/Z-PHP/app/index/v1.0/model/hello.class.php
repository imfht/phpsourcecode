<?php
namespace model;

class hello
{
    public function msg()
    {
        $msg = '
            <h1 style="margin-top:100px;text-align:center">欢迎使用 Z-PHP !</h1>
            <h2 style="text-align:center">' . ZPHP_VER . '</h2>
        ';
        return $msg;
    }
}

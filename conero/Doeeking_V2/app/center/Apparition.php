<?php
namespace app\center;
use hyang\Evil;
class Apparition extends Evil{
    public function pageInit()
    {
        $this->OptAction([
            'title' => '个人中心','home'=>'/conero/center.html'
        ]);
    }
    public function app_nav()
    {
        return '
        ';
    }
}
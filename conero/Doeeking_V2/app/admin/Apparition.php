<?php
namespace app\admin;
use hyang\Evil;
class Apparition extends Evil{
    public function pageInit()
    {
        $this->OptAction([
            'title' => '系统管理'
        ]);
    }
    public function app_nav()
    {
        return '
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/admin/user.html">用户管理</a>
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/admin/textpl.html">系统模板</a>
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/admin/table.html">数据后台</a>
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/admin/sconst.html">系统常量</a>
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/admin/files.html">系统文件一览</a>                  
        ';
        
    }
}
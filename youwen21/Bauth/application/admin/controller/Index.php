<?php
namespace app\admin\controller;

use app\lib\Menu;

class Index extends Base
{
    public function index()
    {
        return $this->fetch('base/index');
    }

    /**
     * 系统介绍
     * @author baiyouwen
     */
    public function introduce()
    {
        return $this->fetch();
    }

    /**
     * 开发者文档
     * @author baiyouwen
     */
    public function developer()
    {
        return $this->fetch();
    }
    // 时间日期案例
    public function datetimepiker_demo()
    {
        return $this->fetch();
    }
    // ajax提交案例
    public function ajaxsubmit_demo()
    {
        return $this->fetch();
    }
    // ajax上传图片案例
    public function ajaxfileupload_demo()
    {
        return $this->fetch();
    }
    // 百度UMeditor案例
    public function umeditor_demo()
    {
        return $this->fetch();
    }
    // Web Uploader
    public function web_uploader_demo()
    {
        return $this->fetch();
    }
    // base64图片上传
    public function base64_demo()
    {
        return $this->fetch();
    }

    // 综合案例
    public function multiple_demo()
    {
        return $this->fetch();
    }
    /**
     * 菜单模块使用案例
     * @author EchoEasy
     * @DateTime 2017-01-13T22:24:30+0800
     */
    public function menu_demo()
    {
        $module = 'admin';
        $menu = new Menu(true);
        $list = $menu->getMenu($module);
        $this->assign('list', $list);
        return $this->fetch();
    }
}

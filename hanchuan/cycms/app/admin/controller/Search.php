<?php
namespace app\admin\controller;

use think\facade\View;
use app\admin\model\Menu;

class Search extends Common
{
    public function index()
    {
        $keyword = Input('post.keyword', '', 'addslashes');
        $list = Menu::where("status=1 and (title like '%{$keyword}%' or url like '%{$keyword}%' or tips like '%{$keyword}%')")->select();
        View::assign('list', $list);
        View::assign('keyword', $keyword);
        return View::fetch();
    }
}

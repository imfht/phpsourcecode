<?php
namespace app\admin\controller;

use app\admin\model\Menu;
use think\facade\View;
use think\exception\HttpResponseException;
use think\Response;

class Js extends Common
{
    public function js()
    {
        $json = array();
        $list = Menu::field('title,url')->where(['status'=>1])->select();
        foreach ($list as $k=>$v) {
            $json[] = $v['title'];
            $json[] = $v['url'];
        }
        
        $header = ["Content-type"=>"text/javascript"];

        View::assign('json', json_encode($json));

        $response = Response()::create('js','View')->header($header);
        throw new HttpResponseException($response);

    }
}

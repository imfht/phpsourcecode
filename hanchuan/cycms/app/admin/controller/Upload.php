<?php
namespace app\admin\controller;

use think\facade\View;
use think\facade\Request;

class Upload extends Common
{
    public function uploadpic($name='image', $width='100', $height='100', $url='')
    {
        $url = base64_decode($url);
        $file = $this->uploadsave($name);
        if ($file) {
            $url = $file;
        }
        View::assign('name', $name);
        View::assign('width', $width);
        View::assign('height', $height);
        View::assign('url', $url);
        return View::fetch();
    }

    public function uploadpics($name='images', $url='')
    {
        $url = base64_decode($url);
        if (Request::isPost()) {
            $images = array_filter(explode('|', input('post.url')));
        } else {
            $images = array_filter(explode('|', $url));
        }
        $file = $this->uploadsave($name);
        if ($file) {
            array_push($images, $file);
        }
        $url = implode('|', $images);

        View::assign('url', $url);
        View::assign('images', $images);
        View::assign('name', $name);
        return View::fetch();
    }

    private function uploadsave($name)
    {
        if (Request::isPost()) {
            $file = request()->file($name);
            try {
                $savename = \think\facade\Filesystem::disk('public')->putFile('',$file);
                $savename = '/static/upload/'.$savename;
            } catch (think\exception\ValidateException $e) {
                //echo $e->getMessage();
                $savename = false;
            }
            
            return $savename;
        }
    }
}

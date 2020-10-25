<?php
/*
* 
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/5/5
*/

namespace app\system\controller;

use app\base\controller\System;
use app\system\model\Picture;

class File extends System
{
    public function upload_img() {
        $files = $this->request->file();
        $ids = '';
        $urls = '';
        foreach ($files as $file){
            $root_path = ROOT_PATH . 'public';
            $path = DS . 'uploads'. DS . date('Ym') . DS;

            $hash = $file->hash();
            $md5 = $file->hash('md5');
            $pic = new Picture();
            if(0 == $pic_info = $pic->check_images($md5,$hash)){
                $info = $file->move($root_path . $path, $hash);
                $path .= $info->getSaveName();
                $pic->save(['path'=>$path, 'md5'=>$md5, 'hash'=>$hash]);
                $pic_info = [];
                $pic_info['id'] = $pic->id;
                $pic_info['path'] = $path;
            }
            $ids .= $pic_info['id'] . ',';
            $urls .= $pic_info['path'] . ',';
        }
        $ids = rtrim($ids,',');
        $urls = rtrim($urls,',');

        echo header("content-type:text/html; charset=utf-8");
        echo json_encode(['ids'=>$ids,'urls'=>$urls]);
    }

}
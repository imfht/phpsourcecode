<?php

namespace app\admin\model;

use think\Model;

class Menu extends Model
{

    //获取树的根到子节点的路径
    public function getPath($id){
        $path = array();
        $nav = $this->where("id={$id}")->field('id,pid,title,url')->find();
        $path[] = $nav;
        if($nav['pid'] >0){
            $path = array_merge($this->getPath($nav['pid']),$path);
        }
        return $path;
    }
}

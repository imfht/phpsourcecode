<?php
namespace app\admin\controller;

use app\common\controller\IndexBase; 

class Market extends IndexBase
{
    public function show($id=0){
        if (empty($id)) {
            //$this->error('id不存在!');
        }
        $this->assign('id',$id);
        return $this->fetch();
    }
}
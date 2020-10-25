<?php
namespace app\member\controller\wxapp;


use app\common\controller\MemberBase;

//小程序 
class User extends MemberBase
{
    public function edit_map($point='113.30764968,23.1200491',$type=0){
        if ($this->request->isAjax() && ($type==1 || empty($this->user['map_x']))) {
            list($x,$y) = explode(',',$point);
            edit_user([
                    'uid'=>$this->user['uid'],
                    'map_x'=>$x,
                    'map_y'=>$y,
            ]);
            return $this->ok_js();
        }else{
            return $this->err_js();
        }
    }
}

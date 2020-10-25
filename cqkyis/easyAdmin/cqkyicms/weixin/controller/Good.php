<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/3 0003
 * Time: 17:47
 */

namespace app\weixin\controller;


use think\Controller;

class Good extends Controller
{

    public function setting(){
        $res = db('good_config')->find();
        if($res){
            return json($res);
        }else{
            return json(['code'=>1]);
        }
    }


    public function infos(){
        $id = input('id');
        $res = db('good')->where('good_id',$id)->find();
        $res['good_img']= request()->root(true).'/uploads/'.$res['good_img'];
        $imgs = db('good_imgs')->where('good_id',$id)->select();
        foreach ($imgs as $k=>$v){
            $imgs[$k]['imgs']=request()->root(true).'/uploads/'.$v['imgs'];
        }
        $res['imgs']=$imgs;
        return json($res);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 10:08
 */

namespace app\weixin\controller;


use think\Controller;

class Address extends Controller
{

    public function add(){
        $data['uid']=input('uid');
        $data['contacts']=input('contacts');
        $data['phone'] = input('phone');
        $data['readdress'] = input('readdress');
        $data['doorno'] = input('doorno');
       if(db('system_user_address')->insert($data)){
           return json(['code'=>1]);
       }else{
           return json(['code'=>2]);
       }
    }

}
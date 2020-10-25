<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/14 15:30
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;


use app\admin\model\WeixinModel;

class Weixin extends Base
{
     protected $title="微信配置";
    public function index(){
        $weixin = new WeixinModel();
        if(request()->isPost()){
           $data = input('post.');

            $res = $weixin->addAndEdit($data);
            if($res['code']==1){

                $this->ky_success($res['msg'],$res['data']);
            }else{

                $this->ky_error($res['msg']);
            }
        }else{
            $rs = $weixin->bytype(1);


          $name="微信配置";
         $this->assign([
            'name'=>$name,
            'title'=>$this->title,
            'wx'=>$rs
         ]);
        return $this->fetch();
        }
    }


    public function app(){

      $weixin = new WeixinModel();
             if(request()->isPost()){
                $data = input('post.');

                 $res = $weixin->aeapp($data);
                 if($res['code']==1){

                     $this->ky_success($res['msg'],$res['data']);
                 }else{

                     $this->ky_error($res['msg']);
                 }
             }else{
                 $rs = $weixin->bytype(2);


               $name="小程序配置";
              $this->assign([
                 'name'=>$name,
                 'title'=>$this->title,
                 'wx'=>$rs
              ]);
             return $this->fetch();
             }

    }

}
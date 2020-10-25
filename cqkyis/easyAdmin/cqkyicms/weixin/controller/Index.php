<?php

/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/15 10:04
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */
namespace app\weixin\controller;
use app\weixin\model\Good;
use app\weixin\model\Goodcate;
use think\Controller;

class Index extends Controller
{

    public function index(){
        $goodcate = new Goodcate();
        $cate = $goodcate->findAll();
        return json($cate);
    }

    public function cate(){
        $id = input('id');
        $good = new Good();
        $cate = $good->findId($id);
      $list=  db('good_advert')->field('advert_img')->where('cate_id',$id)->select();
      foreach ($list as $k=>$v){
          $list[$k]['advert_img']=request()->root(true).'/uploads/'.$v['advert_img'];
      }
        return json(['data'=>$cate,'advert'=>$list]);
 }

}
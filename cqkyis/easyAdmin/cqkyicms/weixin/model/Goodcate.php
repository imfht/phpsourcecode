<?php

/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/15 10:06
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */
namespace app\weixin\model;
class Goodcate extends \think\Model
{

    protected $name="good_cate";

    /*获得一级菜单*/
    public function findAll(){
     return $this->where('parent_id',0)->select();
   }

}
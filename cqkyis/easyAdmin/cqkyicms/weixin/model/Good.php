<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/15 11:41
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\weixin\model;


use think\facade\Env;
use think\Model;

use think\Request;
class Good extends Model
{

    protected $name="good";

    public function findId($id){
        $res = $this->where('cate_id',$id)->select();
        foreach ($res as $key=>$var ){
            $res[$key]['good_img']= request()->root(true).'/uploads/'.$var['good_img'];
        }
        return $res;
    }

}
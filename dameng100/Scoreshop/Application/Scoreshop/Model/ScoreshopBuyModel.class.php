<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Scoreshop\Model;
use Think\Model;
use Think\Page;

class ScoreshopBuyModel extends Model{

	public function getListByPage($map,$page=1,$order='sell_num desc',$field='*',$r=20)
    {
        $totalCount=$this->where($map)->count();
        if($totalCount){
            $list=$this->where($map)->page($page,$r)->order($order)->field($field)->select();
        }
        return array($list,$totalCount);
    }

    public function getList($map,$order='view desc',$limit=5,$field='*')
    {
        $lists = $this->where($map)->order($order)->limit($limit)->field($field)->select();
        return $lists;
    }

    public function getData($id)
    {
        if($id>0){
            $map['id']=$id;
            $data=$this->where($map)->find();
            return $data;
        }
        return null;
    }
}
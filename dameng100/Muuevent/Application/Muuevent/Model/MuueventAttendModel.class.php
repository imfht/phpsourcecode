<?php
namespace Muuevent\Model;
use Think\Model;
use Think\Page;

class MuueventAttendModel extends Model
{

	public function getListByPage($map,$page=1,$order='create_time desc',$field='*',$r=20)
    {
        $totalCount=$this->where($map)->count();
        if($totalCount){
            $list=$this->where($map)->page($page,$r)->order($order)->field($field)->select();
        }
        return array($list,$totalCount);
    }

}
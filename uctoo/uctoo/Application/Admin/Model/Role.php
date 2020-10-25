<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Model;

/**
 * 身份模型
 * Class RoleModel
 * @package Admin\Model
 * @郑钟良
 */
class Role extends Model
{

    protected $autoWriteTimestamp = true;
    protected $auto = ['status'];

    public function setStatusAttr($value = 1)
    {
        return $value;
    }

    /**
     * 分页按照$map获取列表
     * @param array $map 查询条件
     * @param int $page 页码
     * @param $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @param int $r 每页条数
     * @return mixed 一页结果列表
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function selectPageByMap($map=array(),$page=1,$r=20,$order,$fields=null){
        $order=$order?$order:"id asc";
        if($fields==null){
            $list=$this->where($map)->order($order)->page($page,$r)->select();
        }else{
            $list=$this->where($map)->order($order)->field($fields)->page($page,$r)->select();
        }
        $totalCount=$this->where($map)->count();
        return array($list,$totalCount);
    }

    /**
     * 通过$map获取列表
     * @param array $map 查询条件
     * @param $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @return mixed 结果列表
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function selectByMap($map=array(),$order=null,$fields=null){
        $order=$order?$order:"id asc";
        if($fields==null){
            $list=$this->where($map)->order($order)->select();
        }else{
            $list=$this->where($map)->order($order)->field($fields)->select();
        }
        return $list;
    }

    /**
     * * 通过$map获取单条值
     * @param array $map 查询条件
     * @param string $order 排序
     * @param null $fields 查询字段，null表示全部字段
     * @return mixed 结果
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getByMap($map=array(),$order,$fields=null){
        $order=$order?$order:"id asc";
        if($fields==null){
            $data=$this->where($map)->order($order)->find();
        }else{
            $data=$this->where($map)->order($order)->field($fields)->find();
        }
        return $data;
    }


} 
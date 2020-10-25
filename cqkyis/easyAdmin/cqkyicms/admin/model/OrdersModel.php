<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 18:48
 */

namespace app\admin\model;


use think\Model;

class OrdersModel extends Model
{

    protected  $name="order";
    public function getByWhere($where, $offset, $limit)
    {
        return $this->alias('u')->field( 'u.*,nickname')
            ->join('system_user rol', 'u.uid = ' . 'rol.uid')
            ->where($where)->limit($offset, $limit)->order('orderId desc')->select();
    }

    public function getAll($where)
    {
        return $this->where($where)->count();
    }

}
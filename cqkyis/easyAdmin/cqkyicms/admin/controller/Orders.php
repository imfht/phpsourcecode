<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 18:44
 */

namespace app\admin\controller;


use app\admin\model\OrdersModel;

class Orders extends Base
{
    protected $title="订单管理";
    public function index(){
        $name = "订单列表";
        if(request()->isAjax()){
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = '';
            if (!empty($param['searchText'])) {
                $where=' ordercode like "%'. $param['searchText'].'%"';


            }
            $order = new OrdersModel();
            //$good = new GoodModel();
            $res = $order->getByWhere($where, $offset, $limit);
            foreach ($res as $key => $vo) {
                $res[$key]['creattime'] = date('Y-m-d H:i:s', $vo['creattime']);
            }
            $return['total'] = $order->getAll($where);  //总数据
            $return['rows'] = $res;
            $return['sql'] = $order->getLastSql();
            return json($return);


        }else{


        $this->assign([
            'name'=>$name,
            'title'=>$this->title,

        ]);
        }
        return $this->fetch();
    }

}
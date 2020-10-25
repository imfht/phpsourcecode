<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/8
 * Time: 17:15
 */

namespace app\admin\controller;


class Transaction extends Base {
    public function __construct(){
        parent::__construct();
    }

    /**
     * 获取订单
     * @route('admin/transaction/list','get')->allowCrossDomain()
     * @return \think\Response
     */
    public function order_list(){
        $list = [];
        $j = 0;
        for($i=0;$i<50;$i++){
            $j++;
            array_push($list,[
                'order_no'=> build_order_no(),
                'timestamp'=> date('Y-m-d H:i:s',time()+ 20 * $i),
                'username'=> "user{$j}",
                'price'=> rand(100,99999),
                'status'=> $i%5==0 ? true : false
            ]);
        }
        return $this->__s('',$list);
    }
}
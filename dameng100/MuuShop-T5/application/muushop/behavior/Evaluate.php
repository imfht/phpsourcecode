<?php
namespace app\muushop\behavior;

use think\Controller;
use think\Loader;
use think\Route;
use think\Db;
use app\muushop\model\MuushopOrder as Order;

// 评价后执行行为
class Evaluate extends Controller{

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        
        //$content = var_export($params,true)."\n"; //要写入的内容
    	//$this->writeLog($content);

    	$order = model('muushop/MuushopOrder')->getDataByOrderNo($params['order_no']);
    	$order = $order->toArray();
    	//初始化判断结果
    	$result = false;
    	//判断所有商品是否评价
    	foreach($order['products'] as $val){
    		$map['app'] = 'muushop';
            $map['model'] = $params['model'];
            $map['order_no'] = $params['order_no'];

            $row_id = explode(';',$val['sku_id']);
            $map['row_id'] = $row_id[0];
            $map['param'] = $val['sku_id'];
    		$evaluate = Db::name('evaluate')->where($map)->find();
    		if($evaluate){
    			$result = true;
    		}else{
    			$result = false;
    		}
    	}

    	//根据结果执行是否更改订单状态
    	if($result){
    		$data['id'] = $order['id'];
    		$data['status'] = Order::ORDER_COMMENT_OK;
    		model('muushop/MuushopOrder')->editData($data);
    		$content = '更改了订单号：'.$order['order_no'].'的评价状态'."\n"; //要写入的内容
    		$this->writeLog($content);
    	}
    }

    /**
     * 请确保项目文件有可写权限，不然打印不了日志。
     */
    private function writeLog($text) {
        file_put_contents (dirname(__FILE__)."/log.txt", date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
    }
}
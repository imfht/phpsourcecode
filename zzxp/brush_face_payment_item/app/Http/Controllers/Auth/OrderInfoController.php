<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Lib\Api\Api AS Api;
use View,Input,Session,Redirect,Response;
/**
 * Created by PhpStorm.
 * User: xzl
 * Date: 14/10/23
 * Time: 下午4:16
 */
class OrderInfoController extends Controller {

    public function __construct(Request $req){
        $this->api = new Api;
        $this->request = $req;
    }
    public function index(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;   
        }

        $order_info = $this->api->getOrderInfo($param);
        return \View::make('order_info.index',array('order_info'=>$order_info['result'],'total'=>$order_info['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'订单明细管理'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addOrderInfo($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $order_info = $this->api->getOrderInfo(['id'=>$id]);
        return \Response::json(isset($order_info['result']) && isset($order_info['result'][0]) ? $order_info['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        $res = $this->api->updateOrderInfo($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function postDel(){
        $id = $this->request->get('ids','');
        if(empty($id)){
            return '没有选择任何记录';
        }

        $res = $this->api->delOrderInfo(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}
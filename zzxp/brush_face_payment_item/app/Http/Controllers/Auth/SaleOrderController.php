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
class SaleOrderController extends Controller {

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
        if(isset($param['status']) && $param['status'] === ''){
            unset($param['status']);
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;   
        }
        if(isset($param['bid']) && $param['bid'] === ''){
            unset($param['bid']);
        }

        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin']  = true;
        
        $sale_order = $this->api->getSaleOrder($param);
        if(!empty($param['bid'])){
            $business = $this->api->getBusiness(['id'=>$param['bid'],'page'=>1,'size'=>1]);
            isset($business['result']) && $business = $business['result'];
            isset($business[0]) && $business = $business[0];
            $param['rname'] = $business['rname'];
        }

        return \View::make('sale_order.index',array('sale_order'=>$sale_order['result'],'total'=>$sale_order['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'销售订单管理'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addSaleOrder($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $sale_order = $this->api->getSaleOrder(['id'=>$id]);
        return \Response::json(isset($sale_order['result']) && isset($sale_order['result'][0]) ? $sale_order['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        $res = $this->api->updateSaleOrder($data);
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

        $res = $this->api->delSaleOrder(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}
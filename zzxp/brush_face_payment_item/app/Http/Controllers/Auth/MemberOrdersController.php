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
class MemberOrdersController extends Controller {

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
        if(isset($param['status']) && $param['status'] === ''){
            unset($param['status']);
        }
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin'] = true;
        $member_orders = $this->api->getMemberOrders($param);
        return \View::make('member_orders.index',array('member_orders'=>$member_orders['result'],'total'=>$member_orders['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'订单表管理'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addMemberOrders($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $member_orders = $this->api->getMemberOrders(['id'=>$id]);
        return \Response::json(isset($member_orders['result']) && isset($member_orders['result'][0]) ? $member_orders['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        $res = $this->api->updateMemberOrders($data);
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

        $res = $this->api->delMemberOrders(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}
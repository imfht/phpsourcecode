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
 * []
 */
class GoodsController extends Controller {

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
        if(isset($param['type']) && $param['type'] === ''){
            unset($param['type']);
        }

        if(isset($param['status']) && $param['status'] === ''){
            unset($param['status']);
        }


        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin'] = true;

        $goods = $this->api->getGoods($param);
        return \View::make('goods.index',array('goods'=>$goods['result'],'total'=>$goods['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'物料表管理'));
    }
    public function getAdd(){
    }
   
    public function postAdd(){
        $data = $this->request->all();
        if(!empty($data['attsrc'])){
            $data['pic_list'] = json_encode($data['attsrc']);
        }
        $res  = $this->api->addGoods($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $goods = $this->api->getGoods(['id'=>$id]);
        $data = isset($goods['result']) && isset($goods['result'][0]) ? $goods['result'][0] : [];
        isset($data['pic_list']) && $data['pic_list'] = json_decode($data['pic_list']);
        return \Response::json($data);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        if(!empty($data['attsrc'])){
            $data['pic_list'] = json_encode($data['attsrc']);
        }
        $data  = $this->request->all();
        $res = $this->api->updateGoods($data);
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

        $res = $this->api->delGoods(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}
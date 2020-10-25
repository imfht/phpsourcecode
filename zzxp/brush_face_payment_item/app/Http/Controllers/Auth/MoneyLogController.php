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
class MoneyLogController extends Controller {

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
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $param['admin'] = true;

        $money_log = $this->api->getMoneyLog($param);
        return \View::make('money_log.index',array('money_log'=>$money_log['result'],'total'=>$money_log['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'资金流水管理'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addMoneyLog($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $money_log = $this->api->getMoneyLog(['id'=>$id]);
        return \Response::json(isset($money_log['result']) && isset($money_log['result'][0]) ? $money_log['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        $res = $this->api->updateMoneyLog($data);
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

        $res = $this->api->delMoneyLog(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}
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
class MessageController extends Controller {

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

        $message = $this->api->getMessage($param);
        return \View::make('message.index',array('message'=>$message['result'],'total'=>$message['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'消息列表管理'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        if(empty($data['type'])){
            return '请选择消息类型';
        }
        if($data['type'] == 1){
            if(empty($data['phone'])){
                return '接收人电话不能为空';
            }
            $member = $this->api->getMember(['phone'=>$data['phone'],'page'=>1,'size'=>1]);
            isset($member['result']) && $member = $member['result'];
            isset($member[0]) && $member = $member[0];
            if(empty($member)){
                return '接收人信息不存在';
            }
            $data['member_id'] = $member['id'];
        }
        $res  = $this->api->addMessage($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $message = $this->api->getMessage(['id'=>$id]);
        return \Response::json(isset($message['result']) && isset($message['result'][0]) ? $message['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        $res = $this->api->updateMessage($data);
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

        $res = $this->api->delMessage(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}
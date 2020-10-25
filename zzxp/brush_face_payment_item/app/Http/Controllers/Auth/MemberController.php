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
class MemberController extends Controller {

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
        $param['admin'] = true;
        $param['order'] = 'id';
        $param['orderby'] = 'DESC';
        $member = $this->api->getMember($param);
        return \View::make('member.index',array('member'=>$member['result'],'total'=>$member['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'会员表管理'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addMember($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $member = $this->api->getMember(['id'=>$id]);
        isset($member['result']) && $member = $member['result'];
        isset($member[0]) && $member = $member[0];
        unset($member['password']);
        return \Response::json($member);
    }
    public function upPassword(){
        $password = $this->request->get('password','');
        $re_password = $this->request->get('re_password','');
        if($password != $re_password){
            return '两次密码输入不一致';
        }

        $id = $this->request->get('id',0);
        $member = $this->api->getMember(['id'=>$id]);
        isset($member['result']) && $member = $member['result'];
        isset($member[0]) && $member = $member[0];
        if(empty($member)){
            return '用户信息不存在';
        }

        $this->api->updateMember(['id'=>$id,'password'=>\Hash::make($password)]);
        return '1';

    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        $res = $this->api->updateMember($data);
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

        $res = $this->api->delMember(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}
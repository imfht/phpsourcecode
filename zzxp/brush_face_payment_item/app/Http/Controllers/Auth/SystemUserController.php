<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Lib\Api\AdminApi AS AdminApi;
use View,Input,Session,Redirect,Response;
/**
 * Created by PhpStorm.
 * User: xzl
 * Date: 14/10/23
 * Time: 下午4:16
 */
class SystemUserController extends Controller {

    public function __construct(Request $req){
        $this->check();
        $this->share_car = new AdminApi;
        $this->request = $req;
    }
    public function index(){

        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        $param['order'] = 'created_at';
        $param['orderby']  = 'DESC';
        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        $nick_name=$this->request->get('nick_name','');
        $system_role_id=$this->request->get('system_role_id','');
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        $param['is_admin'] = true;
        $param['order'] = 'system_user_id';
        $param['orderby'] = 'DESC';
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }

        if(Session::get('grade')!=1){
            $param['creator']=Session::get('sys_id');
        }
        //$system_user = $this->share_car->getSystemUser(['creator'=>Session::get('sys_id'),'is_admin'=>true]);

        $system_user = $this->share_car->getSystemUser($param);//print_r($system_user);exit;

        $system_role = $this->share_car->getSystemRole(['order'=>'created_at','orderby'=>'ASC']);
        isset($system_role['result']) && $system_role = $system_role['result'];//print_r($system_role);exit;

        //获取该管理员创建的角色
        $myRole=$this->share_car->getSystemRole(['creator'=>Session::get('sys_id')]);//print_r($myRole);exit;


        return \View::make('system_user.index',array('system_user'=>$system_user,'total'=>$system_user['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'系统管理员表管理','system_role'=>$system_role,'nick_name'=>$nick_name,'system_role_id'=>$system_role_id,'myRole'=>$myRole));

    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        //加入创建人的id
        $data['creator']=Session::get('sys_id');
        //增加的：
        $data['password'] = \Hash::make($data['password']);
        $res  = $this->share_car->addSystemUser($data);
        if(empty($res)){
            return $this->share_car->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        //$id = $this->request->get('id',0);
        $id = $this->request->get('system_user_id',0);
        //增加的：
        $password = $this->request->get('password','');
        //$system_user = $this->share_car->getSystemUser(['id'=>$id]);
        $system_user = $this->share_car->getSystemUser(['system_user_id'=>$id]);
        //增加的：
        $data['password'] = \Hash::make($password);
        return \Response::json(isset($system_user['result']) && isset($system_user['result'][0]) ? $system_user['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();
        //判断，如果改了密码（password不为空），则进入判断：
        if(!empty($data['password'])){
            $data['password']=\Hash::make($data['password']);
        }

        //print_r($data);exit;
        $res = $this->share_car->updateSystemUser($data);
        if(empty($res)){
            return $this->share_car->getErr();
        }else{
            return '1';
        }
    }

    //管理员修改自己的密码
    public function editMyselfPassword(){
        $id = Session::get('sys_id');
        $system_user = $this->share_car->getSystemUser(['system_user_id'=>$id]);
        return \Response::json(isset($system_user['result']) && isset($system_user['result'][0]) ? $system_user['result'][0] : []);
    }

    //管理员修改自己的密码
    public function updateMyselfPassword(){
        $data  = $this->request->all();

        $id = Session::get('member_id');
        $system_user = $this->share_car->getSystemUser(['system_user_id'=>$id]);
        isset($system_user['result']) && $system_user = $system_user['result'];
        isset($system_user[0]) && $system_user = $system_user[0];

        if(!empty($data['password']) || $data['password']!=''){
            //判断，如果输入的原密码和数据库中原密码不想等则return错误信息(如果提交的$data存在originalPassword，则进行比较。否则不验证，直接修改密码)
            if(!\Hash::check($data['originalPassword'],$system_user['password'])){
                return '<b style="color:red;">原密码错误,不能进行修改</b>';
            }else{
                //unset掉为了判断而发送过来的原密码和输入的原密码
                unset($data['yuan_md5_password']);
                unset($data['originalPassword']);
            }
            $data['password']= \Hash::make($data['password']);
        }else{
            unset($data['password']);
        }
        $data['member_id'] = Session::get('member_id');
        $res = $this->share_car->updateSystemUser($data);
        if(empty($res)){
            return $this->share_car->getErr();
        }else{
            return '1';
        }
    }

    public function postDel(){
        $id = $this->request->get('ids','');
        if(empty($id)){
            return '没有选择任何记录';
        }

        $res = $this->share_car->delSystemUser(['id'=>$id]);
        if($res === false){
            return $this->share_car->getErr();
        }else{
            return '1';
        }
    }
}
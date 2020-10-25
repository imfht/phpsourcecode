<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Lib\Api\AdminApi AS AdminApi;
use View,Input,Session,Redirect,Response,Cache;
/**
 * Created by PhpStorm.
 * User: xzl
 * Date: 14/10/23
 * Time: 下午4:16
 */
class SystemRoleController extends Controller {

    public function __construct(Request $req){
        $this->check();
        $this->admin = new AdminApi;
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
        if(!empty($start_time)){
            $param['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($param['created_at'])){
                $param['created_at'][] = '';
            }
            $param['created_at'][] = $end_time;
        }

        $system_role = $this->admin->getSystemRole($param);//print_r($system_role);//exit;

        $brandList = $this->admin->getBrand([]);//print_r($brandList);exit;

        //获取当前用户（管理员的sys_id，system_role表中的creator）
        $creator=Session::get('sys_id');

        //只保留自己创建的角色(creator为当前管理员sys_id的项)
        foreach($system_role['result'] as $k=>$v){
            //如果不是属于当前管理员创建，且不是超级管理员，则unset掉
            if($v['creator']!=$creator && Session::get('grade')!=1){
                unset($system_role['result'][$k]);
            }
        }

        //获取当前用户(管理员)所属的角色(system_role)的所有权限(system_menu)
        $allSystemRole=$this->admin->getSystemRole(['system_role_id'=>Session::get('system_role_id')])['result'][0];
        $theSystemRole=$allSystemRole['menu_list'];
        //将$TheSystemRole拆成数组
        $theMenuArr=explode(',',$theSystemRole);//print_r($theMenuArr);exit;

        //获得所有的权限
        $system_menu = $this->admin->getSystemMenu(['order'=>'created_at','orderby'=>'ASC']);//print_r($system_menu);exit;

        $newArr=[];
        foreach($system_menu['result'] as $k=>$v){
            $newArr[]=$v['system_menu_id'];
        }


        //如果不是超级管理员，则只显示自己有的权限。如果是超级管理员，则显示所有的权限
        if(Session::get('grade')!=1){
            //只保留当前管理员所拥有的权限
            foreach($system_menu['result'] as $k=>$v){
                //如果包含在内，不做处理，并将$theMenuArr中该项目unset掉(提高程序效率)
                if(in_array($v['system_menu_id'],$theMenuArr)){
                    unset($theMenuArr[array_search($v['system_menu_id'],$theMenuArr)]);
                }else{
                    unset($system_menu['result'][$k]);
                }
            }
        }




        isset($system_menu['result']) && $system_menu = $system_menu['result'];

        return \View::make('system_role.index',array('system_role'=>$system_role['result'],'system_menu'=>$system_menu,'total'=>$system_role['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'角色表管理','brandList'=>$brandList,'theRoleId'=>$allSystemRole['system_role_id']));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();

        //加入创建人的id
        $data['creator']=Session::get('sys_id');
        if(!empty($data['menu_list'])){
            $data['menu_list'] = implode(',',$data['menu_list']);
        }
        $res  = $this->admin->addSystemRole($data);
        if(empty($res)){
            return $this->admin->getErr();
        }else{
            //生成缓存
            $menu = $this->admin->getSystemMenu(['system_menu_id'=>explode(',',$data['menu_list'])]);
            isset($menu['result']) && $menu = $menu['result'];
            // \Log::info($res);
            Cache::forever('system_menu_role_'.$res['system_role_id'],$menu);
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $system_role = $this->admin->getSystemRole(['system_role_id'=>$id]);
        return \Response::json(isset($system_role['result']) && isset($system_role['result'][0]) ? $system_role['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('system_role_id',0);
        $data  = $this->request->all();

        if(!empty($data['menu_list'])){
            $data['menu_list'] = implode(',',$data['menu_list']);
        }
        $data['system_role_id'] = $id;
        $res = $this->admin->updateSystemRole($data);
        if(empty($res)){
            return $this->admin->getErr();
        }else{

            //生成缓存
            $menu = $this->admin->getSystemMenu(['system_menu_id'=>explode(',',$data['menu_list'])]);
            isset($menu['result']) && $menu = $menu['result'];
            Cache::forget('system_menu_role_'.$id);
            Cache::forever('system_menu_role_'.$id,$menu);
            return '1';
        }
    }


    //更新权限缓存(都有权限)
    public function updateCache(){
        //获取当前用户的角色id
        $id=Session::get('system_role_id');
        if(empty($id)){
            exit;
        }
        $menu_list=$this->admin->getSystemRole(['system_role_id'=>$id])['result'][0]['menu_list'];
        //生成缓存
        $menu = $this->admin->getSystemMenu(['system_menu_id'=>explode(',',$menu_list),'order'=>'sort','orderby'=>'asc']);
        isset($menu['result']) && $menu = $menu['result'];
        Cache::forget('system_menu_role_'.$id);
        Cache::forever('system_menu_role_'.$id,$menu);
        return '1';
    }


    public function postDel(){
        $id = $this->request->get('ids','');
        if(empty($id)){
            return '没有选择任何记录';
        }

        $res = $this->admin->delSystemRole(['id'=>$id]);
        if($res === false){
            return $this->admin->getErr();
        }else{
            return '1';
        }
    }
}
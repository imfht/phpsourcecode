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
class SystemMenuController extends Controller {

    public function __construct(Request $req){
    	$this->check();
        $this->admin = new AdminApi;
        $this->request = $req;
    }

    public function index(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',10);
        $param = $this->request->all();
        // $param['page'] = $page;
        // $param['size'] = $size;
        $param['order'] = 'orders';
        $param['orderby']  = 'asc';
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

        $system_menu = $this->admin->getSystemMenu($param);

        // $system_pmenu = $this->admin->getSystemMenu(['parent_id'=>0]);
        // isset($system_pmenu['result']) && $system_pmenu = $system_pmenu['result'];

        return \View::make('system_menu.index',array('system_menu'=>$system_menu['result'],'total'=>$system_menu['total'],'size'=>$size,'search'=>$param,'web_title'=>'权限表管理'));
    }

    public function getAdd(){
    }

    public function postAdd(){
        $data = $this->request->all();

        if($data['orders']<0) return '排序不能小于0';
        if(!is_numeric($data['orders'])) return '排序必须是数字';

        $res  = $this->admin->addSystemMenu($data);
        if(empty($res)){
            return $this->admin->getErr();
        }else{
            return '1';
        }
    }

    public function getEdit(){
        $system_menu_id = $this->request->get('system_menu_id',0);
        $system_menu = $this->admin->getSystemMenu(['system_menu_id'=>$system_menu_id]);
        return \Response::json(isset($system_menu['result']) && isset($system_menu['result'][0]) ? $system_menu['result'][0] : []);
    }

    public function postUpdate(){
        $id = $this->request->get('system_menu_id',0);
        $data  = $this->request->all();

        if($data['orders']<0) return '排序不能小于0';
        if(!is_numeric($data['orders'])) return '排序必须是数字';

        $res = $this->admin->updateSystemMenu($data);
        if(empty($res)){
            return $this->admin->getErr();
        }else{
            return '1';
        }
    }

    public function postDel(){
        $id = $this->request->get('ids','');
        if(empty($id)){
            return '没有选择任何记录';
        }

        $res = $this->admin->delSystemMenu(['system_menu_id'=>$id]);
        if($res === false){
            return $this->admin->getErr();
        }else{
            return '1';
        } 
    }

}
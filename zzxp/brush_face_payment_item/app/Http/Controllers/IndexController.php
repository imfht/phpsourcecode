<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Lib\Api\AdminApi AS AdminApi;
use App\Lib\Api\Api AS Api;
use Illuminate\Support\Facades\Cache;
use Session;

use Illuminate\Http\Request;


class IndexController extends Controller
{

    public function __construct(Request $req){
        $this->check();
        $this->admin = new AdminApi;
        $this->api = new Api;
        $this->request = $req;
    }

	public function index()
	{
        $role_id = \Session::get('system_role_id','');
        if(empty($role_id)){
            return \Redirect::to('/login/out');
        }
        $menu = Cache::forget('system_menu_role_'.$role_id,'');//Cache::forget('system_menu_role_'.$role_id);
        // if(empty($menu) || !is_array($menu)){
            $admin_api      = new AdminApi;
            $system_role    = $admin_api->getSystemRole(['system_role_id'=>$role_id]);
        

            isset($system_role['result']) && $system_role = $system_role['result'];
            isset($system_role[0]) && $system_role = $system_role[0];

            // \Log::info($role_id);
            // \Log::info($system_role);
            if($role_id == 1){
                $menu = $admin_api->getSystemMenu(['order'=>'orders','orderby'=>'asc']);

            }else{
                $menu = $admin_api->getSystemMenu(['system_menu_id'=>explode(',',$system_role['menu_list']),'order'=>'orders','orderby'=>'asc']);
            }
            // $menu=$this->admin->getSystemMenu(['system_role_id'=>$role_id,'order'=>'sort','orderby'=>'asc']);
            isset($menu['result']) && $menu = $menu['result'];

            
        // var_dump($menu);exit();
            if(empty($menu)){
                return \Redirect::to('/login/out');
            }
            //\Log::info($menu);
            $bool = Cache::forever('system_menu_role_'.$role_id,$menu);

        // }//$menu=$this->admin->getSystemMenu(['system_role_id'=>$role_id,'order'=>'sort','orderby'=>'asc'])['result'];
		return \View::make('index.index',['menu'=>$menu]);
	}
    public function welcome(){

        // $data = $this->api->adminSum([]);
        // var_dump($data);exit();
        // print_r($data);exit();
        return \View::make('index.welcome',['data'=>[]]);

        
    }
    public function getStatictis(){
        $data = $this->request->all();

        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        if(!empty($start_time)){
            $data['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($data['created_at'])){
                $data['created_at'][] = '';
            }
            $data['created_at'][] = $end_time;   
        }



        $result = $this->api->memberStatistics($data); 
        if($result === false){
            return response()->json(['status'=>-1,'error'=>$this->api->getErr()]);
        }  
        return response()->json($result);
    }

    public function getPay(){
        $data = $this->request->all();

        $start_time = $this->request->get('start_time','');
        $end_time   = $this->request->get('end_time','');
        if(!empty($start_time)){
            $data['created_at'][] = $start_time;
        }
        if(!empty($end_time)){
            if(empty($data['created_at'])){
                $data['created_at'][] = '';
            }
            $data['created_at'][] = $end_time;   
        }

        $result = $this->api->memberPay($data); 
        if($result === false){
            return response()->json(['status'=>-1,'error'=>$this->api->getErr()]);
        }
        return response()->json($result);
    }

    public function member(){
        $data = $this->request->all();
        $result = $this->api->member($data); 
        if($result === false){
            return response()->json(['status'=>-1,'error'=>$this->api->getErr()]);
        }
        return response()->json($result);
    }

    // public function getStatictis(){
    //     $data = $this->request->all();
    //     $this->api->getStatictis($data);   
    // }
}
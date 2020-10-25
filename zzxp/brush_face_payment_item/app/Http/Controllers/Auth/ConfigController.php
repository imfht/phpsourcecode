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
class ConfigController extends Controller {

    public function __construct(Request $req){
        $this->api = new Api;
        $this->request = $req;
    }
    public function index(){
        $page = $this->request->get('page',1);
        $size   = $this->request->get('size',1);
        $param = $this->request->all();
        $param['page'] = $page;
        $param['size'] = $size;
        $param['type'] = 1;
     

        $config = $this->api->getConfig($param);
        isset($config['result']) && $config = $config['result'];
        isset($config[0]) && $config = $config[0];
        $data = json_decode($config['param'],true);
        $data['id'] = $config['id'];

        return \View::make('config.index',['config'=>$data]);
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addConfig($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',1);
        $bank = $this->api->getConfig(['id'=>$id]);
        return \Response::json(isset($bank['result']) && isset($bank['result'][0]) ? $bank['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('id',1);
        $data  = $this->request->all();
        $param = [
            'id' => $id,
            'param' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ];
        $res = $this->api->updateConfig($param);
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

        $res = $this->api->delConfig(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }
}
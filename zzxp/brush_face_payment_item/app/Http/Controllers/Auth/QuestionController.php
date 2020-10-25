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
class QuestionController extends Controller {

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

        $question = $this->api->getQuestion($param);
        return \View::make('question.index',array('question'=>$question['result'],'total'=>$question['total'],'size'=>$param['size'],'search'=>$param,'web_title'=>'管理'));
    }
    public function getAdd(){
    }
    public function postAdd(){
        $data = $this->request->all();
        $res  = $this->api->addQuestion($data);
        if(empty($res)){
            return $this->api->getErr();
        }else{
            return '1';
        }
    }
    public function getEdit(){
        $id = $this->request->get('id',0);
        $question = $this->api->getQuestion(['id'=>$id]);
        return \Response::json(isset($question['result']) && isset($question['result'][0]) ? $question['result'][0] : []);
    }
    public function postUpdate(){
        $id = $this->request->get('id',0);
        $data  = $this->request->all();

        $question = $this->api->getQuestion(['id'=>$id]);
        isset($question['result']) && $question = $question['result'];
        isset($question[0]) && $question = $question[0];

        if(!empty($data['answer']) && empty($question['answer'])){
            $data['answer_name'] = \Session::get('user_name','');
            $data['answer_time'] = date('Y-m-d H:i:s');
            $data['status'] = 2;
        }

        $res = $this->api->updateQuestion($data);
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

        $res = $this->api->delQuestion(['id'=>$id]);
        if($res === false){
            return $this->api->getErr();
        }else{
            return '1';
        } 
    }

     //上传图片
    public function upload(){

        $url = $this->request->get('url');
        $action = $this->request->get('actions','file');
        $input_name = $this->request->get('input_name','src');
        if(isset($_FILES[$action])){
            $file = $_FILES[$action];
        }
        // print_r($_FILES);
        if(empty($file)){
            return \View::make('question.upload',array('error'=>-3));
        }

        $fileName = explode('.',$file['name']);
        $name = $file['name'];

        $fix = strtolower(array_pop($fileName));
        $data = ['jpg','jpeg','bmp','png','gif'];
        if(!in_array($fix, $data)){ 
            return \View::make('question.upload',array('error'=>-2));
        }
        $size = $file["size"];
        if($size > 5*1024*1024){
            return \View::make('question.upload',array('error'=>-1));
        }

        $fileAlias = $file["tmp_name"];
        $path = date('Ym');
        $fileName = 'uploadfile/'.$path.'/'.date('YmdHis').rand(1,100000).'.'.$fix;

        if(!is_dir('uploadfile')){
            mkdir('uploadfile');
        }
         if(!is_dir('uploadfile/'.$path)){
            mkdir('uploadfile/'.$path);
        }
        
        
        $path = public_path($fileName);

        move_uploaded_file($fileAlias, $path);
        $this->api->addAttachment([
            'src'=>$path,
            'size'=>$size,
            'nid'=>0,
        ]);

        return \View::make('question.upload',array('path'=>$fileName,'url'=>$url,'name'=>$name,'error'=>'','action'=>$action,'input_name'=>$input_name));
        // return $this->getResult(['path'=>$fileName,'domain'=>$_SERVER['APP_URL']]);
        
    }
}
<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Lib\Alioss\AliossApi;
//use JohnLui\AliyunOSS;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Illuminate\Http\File;

class TestossController extends Controller{

    public function __construct(Request $req){
        $this->request = $req;
    }
    //原版上传方式
    public function index(){

//        var_dump($_POST);
//        $file = $this->request->get('file');
//        file_put_contents('11.jpg',$file);
//        return '11.jpg';
//        var_dump($file);exit();
//        $file = $this->request->all('file');


        $fileName = explode('.',$_FILES['file']['name']);
        $fileAlias = $_FILES["file"]["tmp_name"];
        $fileName = date('YmdHis').'.'.array_pop($fileName);
        
        $path = public_path($fileName);

        move_uploaded_file($fileAlias, $path);
        return $path;
        // if($fileAlias){
        //     $fileName=time().rand();
        //     if(move_uploaded_file($fileAlias, "uploadfile/" .$fileName )){
        //         $alioss = new AliossApi();
        //         //上传图片，并删除原来本地图片
        //         if($data = $alioss->uploadFile('uploadfile/'.$fileName)){

        //             return $data['img_url'];

        //         }

        //     }
        // }

        /*$alioss = new AliossApi();
        //上传图片，并删除原来本地图片
        $data = $alioss->uploadFile('uploadfile/$fileName');
        echo '<pre>';var_dump($data);*/


//        $alioss = new AliossApi();
//        $data = $alioss->uploadFile($fileAlias,$fileName);
//        var_dump($fileName);
//        var_dump($data);exit();
//        if($fileAlias){
//            move_uploaded_file($fileAlias, "uploadfile/" . $fileName);
//        }
        //echo 'fileName: ' . $fileName . ', fileType: ' . $type . ', fileSize: ' . ($size / 1024) . 'KB';


        /*$alioss = new AliossApi();
        //上传图片，并删除原来本地图片
        $data = $alioss->uploadFile('D:/test.png');
        echo '<pre>';var_dump($data);*/


    }

    //通过get方式上传绝对路径，再对绝对路径进行处理
   /* public function index($path){
        echo $path,'<br/>';
        $res=str_replace('($)','/',$path);
        echo $res;

        $alioss = new AliossApi();
        //上传图片，并删除原来本地图片
        $data = $alioss->uploadFile($res);
        echo '<pre>';var_dump($data);


//        $alioss = new AliossApi();
//        //上传图片，并删除原来本地图片
//        $data = $alioss->uploadFile('D:/test.png');
//        echo '<pre>';var_dump($data);


    }*/


}
<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/12 9:16
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;


use think\Controller;
use think\Image;

use think\File;
use think\facade\Env;
class Upload extends Controller
{

    public function index(){
        return $this->fetch();
    }

    /*
     * 单文件上传
     */
    public function upload(){
      $mface = input('face');
        $w = input('wight');
        $h=input('height');
       // return json($id);
        $file = request()->file('file');
        if($file){
            $info = $file->rule('uniqid')->move( Env::get('root_path') .'uploads/'.$mface.'/');
            if($info){

                /**
               * 对头像进行处理
               */
                $image = Image::open(Env::get('root_path') .'uploads/'.$mface.'/'.$info->getFilename());

                    $image->thumb($w, $h)->save(Env::get('root_path') .'uploads/'.$mface.'/'.$info->getFilename());



                $filename =$info->getFilename();
                $AllUrl = $mface.'/'.$info->getFilename();
                return json(['code'=>1,'data'=>$filename,'urls'=>$AllUrl]);
            }else{
                return json(['code' => '2', 'msg' => '文件上传失败']);
            }
        }

    }

    public function del(){

        $path = input('path');

        $filepath=Env::get('root_path')."/Uploads/".$path;

        if(is_file($filepath)){
            unlink($filepath);
        }
        return json(['code'=>'1']);
    }


}
<?php
namespace app\index\controller;

use app\chat\model\ChatContent;
use app\chat\model\ChatGroup;
use app\lib\ChatRedis;
use ImalH\PDFLib\PDFLib;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function pdf(){
        $pdflib = new PDFLib();
        $pdf_file_path = '../data/test.pdf';
        //p(file_get_contents('../data/test.txt'));die;
        $folder_path_for_images = '../data/test.png';
        $pdflib->setPdfPath($pdf_file_path);
        $pdflib->setOutputPath($folder_path_for_images);
        $pdflib->setImageFormat(PDFLib::$IMAGE_FORMAT_PNG);
        $pdflib->setDPI(300);
        $pdflib->setPageRange(1,$pdflib->getNumberOfPages());
        $pdflib->convert();
    }

    public function upload()
    {
        $path = '';
        if(request()->isPost()){
            $file = request()->file('image');
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->move( './uploads');
            if($info){
                $path = './uploads/'.$info->getSaveName();
                $image = \think\Image::open($path);
                $image->thumb(150, 150,\think\Image::THUMB_SCALING)
                    ->save($path,$info->getExtension());

            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        $this->assign('image', $path);
        return view();
    }

    public function test(){
        $chat = new ChatRedis();
        $dta = json_encode(['user'=>['user_id'=>1,'username'=>'威威']]);
        $data = $chat->bindUser(1,$dta)->getUser(1);
        $data['type'] = 'user_info';
        return json($data);
    }

    /**
     *
     */
    public function get_user(){
        $m = new ChatGroup();
        $list = $m->getFriendsByMemberId(1);
        $list = $list->toJson();
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->delete('keys');
        $redis->rPush('keys',$list);
        $result = $redis->lRange('keys', 0, -1);
        p($result);
    }

    public function set_user(){
        $json = '[{"from":1,"to":2,"create_time":1555811757,"type":"friend","content":"\u4f60\u597d \u5728\u5417","status":2},{"from":1,"to":2,"create_time":1555811783,"type":"friend","content":"\u6ca1\u4e8b\u4e0d\u80fd\u548c\u4f60\u804a\u804a","status":2}]';
        $data = json_decode($json,true);
        $chat = new ChatContent();
        $chat->insertAll($data);
        //p($data);die;
    }
}

<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\Db;


/*
 * 系统更新
 *
 * */

class Updates extends Basics
{

    // 初始化
    protected function initialize()
    {
        //初始化模型
        $this->model_name = 'Admin';
        $this->new_model();
        parent::initialize();
    }
    /*
     * 系统更新
     *
     * */
    public function index()
    {
        $this->updates_system();
//        return view();
    }

    //更新系统
    public function updates_system(){
        //下载文件到指定目录
        $url = "http://www.hotel.xyz/test.zip";
        $save_dir = "down";
        $filename = "test.zip";
        if(is_dir('./down')){
            if(rmdir('./down')) echo '目录删除成功！';
        }else{
            echo "目录不存在！";
        }
        $res = $this->getFile($url, $save_dir, $filename, 1);

        //解压文件到指定目录
        $unzip_url = substr(__DIR__,0,23).'public/'.$save_dir.'/'.$filename;
        $unzip_dir = substr(__DIR__,0,23).'public/'.$save_dir;
        dump($unzip_url);
        $this->unzip($unzip_url,$unzip_dir);

        //更新或创建sql
        $this->update_sql('1.01');
    }

    /*
     * 下载文件（适合所有文件下载）
     * */
    function getFile($url, $save_dir = '', $filename = '', $type = 0) {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
            }
        if (0 !== strrpos($save_dir, '/')) {
        $save_dir.= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename
        );
    }


     /**
      * zip解压方法
      * @param string $filePath 压缩包所在地址 【绝对文件地址】d:/test/123.zip
      * @param string $path 解压路径 【绝对文件目录路径】d:/test
      * @return bool
      */
     function unzip($file, $path) {
         $zip = new \ZipArchive();
         $openRes = $zip->open($file);
         dump($file);
         dump($openRes);
         if ($openRes === TRUE) {
             $zip->extractTo($path);
             dump( $zip->extractTo($path));
             $zip->close();
         }
     }

    /*
     * 更新或创建数据表
     * $edition 版本号
     * */
    public function update_sql($edition){
        if($edition == '1.01'){
            $res = Db::query("select * from room where id=:id", ['id' => 5]);
            dump($res);
        }

    }
}

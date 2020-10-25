<?php
namespace Modules\File\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Core\Mvc\ModelQuery;
use Modules\File\Library\FileHandle;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Modules\File\Models\File;

class IndexController extends Controller
{
    public function indexAction()
    {
        extract($this->variables['router_params']);
        $fileModel = File::findFirst($id);
        if ($fileModel) {
            return $this->notFount();
        }
        $paramsAccess = str_split($fileModel->access, 1);
        if ($paramsAccess[0] != '1') {
            return $this->notFount();
        }
        $contentTypeList = Config::get('contentType');
        $sourceFile = ROOT_DIR . $fileModel->name; //要下载的临时文件名
        $outFile = $fileModel->name; //下载保存到客户端的文件名
        $fileExtension = strtolower($fileModel->content_type); //获取文件扩展名
        //echo $sourceFile;
        if (!isset($contentTypeList[$fileExtension])) {
            exit('非法资源下载');
        }

        //检测文件是否存在
        if (!is_file($sourceFile)) {
            die("<b>404 File not found!</b>");
        }
        $len = filesize($sourceFile); //获取文件大小
        $filename = $fileModel->name; //获取文件名字
        $outFile_extension = strtolower($fileModel->content_type); //获取文件扩展名

        $ctype = $contentTypeList[$fileExtension];
        //Begin writing headers

        foreach (array(
            'Cache-Control' => 'public',
            'Content-Type' => $ctype,
            'Content-Disposition' => 'attachment; filename=' . $outFile,
            'Accept-Ranges' => 'bytes',
        ) as $key => $value) {
            $this->response->setHeader($key, $value);
        }

        $size = $len;
        //如果有$_SERVER['HTTP_RANGE']参数
        if (isset($_SERVER['HTTP_RANGE'])) {
            /*Range头域 Range头域可以请求实体的一个或者多个子范围。
            例如，
            表示头500个字节：bytes=0-499
            表示第二个500字节：bytes=500-999
            表示最后500个字节：bytes=-500
            表示500字节以后的范围：bytes=500-
            第一个和最后一个字节：bytes=0-0,-1
            同时指定几个范围：bytes=500-600,601-999
            但是服务器可以忽略此请求头，如果无条件GET包含Range请求头，响应会以状态码206（PartialContent）返回而不是以200 （OK）。
             */
            // 断点后再次连接 $_SERVER['HTTP_RANGE'] 的值 bytes=4390912-
            list($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
            //if yes, download missing part
            str_replace($range, "-", $range); //这句干什么的呢。。。。
            $size2 = $size - 1; //文件总字节数
            $new_length = $size2 - $range; //获取下次下载的长度
            header("HTTP/1.1 206 Partial Content");
            header("Content-Length: $new_length"); //输入总长
            header("Content-Range: bytes $range$size2/$size"); //Content-Range: bytes 4908618-4988927/4988928 95%的时候
        } else {
            //第一次连接
            $size2 = $size - 1;
            header("Content-Range: bytes 0-$size2/$size"); //Content-Range: bytes 0-4988927/4988928
            header("Content-Length: " . $size); //输出总长
        }
        //打开文件
        $fp = fopen("$sourceFile", "rb");
        //设置指针位置
        fseek($fp, $range);
        //虚幻输出
        while (!feof($fp)) {
            //设置文件最长执行时间
            set_time_limit(0);
            print(fread($fp, 1024 * 32)); //输出文件
            flush(); //输出缓冲
            ob_flush();
            sleep(1);
        }
        fclose($fp);
        exit();
    }

    public function imagesBoxListAction()
    {
        extract($this->variables['router_params']);
        $query = array('conditions' => 'uid = :uid:','bind'=> array());
        if($this->user->isLogin()){
            if(!$this->user->isAdmin()){
                $query['bind']['uid'] = $this->user->id;
            }else{
                $query = array();
            }
        }else{
            $query['bind']['uid'] = 0;
        }
        $query['order'] = 'changed DESC';
        $results = File::find($query);
        $data   = new PaginatorModel(
            array(
                "data"  => $results,
                "limit" => 20,
                "page"  => $page
            )
        );
        $this->variables += array(
            '#templates' => 'imagesBoxList',
            'data' => $data->getPaginate(),
        );
    }

    public function imagesBoxUploadAction()
    {
        extract($this->variables['router_params']);
        $output = FileHandle::upload();
        $this->variables += array(
            '#templates' => 'imagesBoxUpload',
        );
        $data = array();
        foreach ($output['success'] as $succes){
            $data[$succes['fileName']] = array(
                'url' => $succes['url'],
                'newName' => $succes['newName']
            );
        }
        if ($this->request->isPost() && !empty($output)) {
            $this->variables['#templates'] = 'json';
            $this->variables['data'] = $data;
        }
    }
}

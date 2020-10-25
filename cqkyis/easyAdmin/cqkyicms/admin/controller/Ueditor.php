<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/1/10 10:16
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;

use think\facade\Env;
class Ueditor
{

    public function index(){
        //return file_get_contents(Env::get('config_path')."config.json");
        //header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
        //header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
        date_default_timezone_set("Asia/chongqing");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");

        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(Env::get('config_path')."config.json")), true);
        //return $CONFIG;

        $action = htmlspecialchars($_GET['action']);
        //dump($CONFIG) ;
        //dump(request());
        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
                $this->config = array(
                    "pathFormat" => $CONFIG['imagePathFormat'],
                    "maxSize" => $CONFIG['imageMaxSize'],
                    "allowFiles" => $CONFIG['imageAllowFiles']
                );
                $this->fieldName = $CONFIG['imageFieldName'];
                $result = $this->uploadfile();
                break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
                $this->config = array(
                    "pathFormat" => $CONFIG['scrawlPathFormat'],
                    "maxSize" => $CONFIG['scrawlMaxSize'],
                    "allowFiles" => $CONFIG['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $this->fieldName = $CONFIG['scrawlFieldName'];
                $result = $this->uploadfilebase64();
                break;
            /* 上传视频 */
            case 'uploadvideo':
                $this->config = array(
                    "pathFormat" => $CONFIG['videoPathFormat'],
                    "maxSize" => $CONFIG['videoMaxSize'],
                    "allowFiles" => $CONFIG['videoAllowFiles']
                );
                $this->fieldName = $CONFIG['videoFieldName'];
                $result = $this->uploadfile('');
                break;
            /* 上传文件 */
            case 'uploadfile':
                $this->config = array(
                    "pathFormat" => $CONFIG['filePathFormat'],
                    "maxSize" => $CONFIG['fileMaxSize'],
                    "allowFiles" => $CONFIG['fileAllowFiles']
                );
                $this->fieldName = $CONFIG['fileFieldName'];
                $result = $this->uploadfile('');
                break;

            /* 列出图片 */
            case 'listimage':
                $allowFiles = $CONFIG['imageManagerAllowFiles'];
                $listSize = $CONFIG['imageManagerListSize'];
                $path = $CONFIG['imageManagerListPath'];
                $result = $this->listfile($allowFiles,$listSize,$path);
                break;
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $CONFIG['fileManagerAllowFiles'];
                $listSize = $CONFIG['fileManagerListSize'];
                $path = $CONFIG['fileManagerListPath'];
                $result = $this->listfile($allowFiles,$listSize,$path);
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $this->config = array(
                    "pathFormat" => $CONFIG['catcherPathFormat'],
                    "maxSize" => $CONFIG['catcherMaxSize'],
                    "allowFiles" => $CONFIG['catcherAllowFiles'],
                    "oriName" => "remote.png"
                );
                $this->fieldName = $CONFIG['catcherFieldName'];
                $result = $this->saveremote();
                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }

    public function uploadfile($savename = true){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($this->fieldName);
        // 移动到框架应用根目录/public/uploads/ 目录下
        $movepath = Env::get('root_path') . $this->config['pathFormat'];
        $ext = str_replace('.','',implode(',',$this->config['allowFiles']));
        $info = $file->validate(['size'=>$this->config['maxSize'],'ext'=>$ext])->move($movepath,$savename);
        //$info = $file->validate(['size'=>1024000,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . '/uploads/image/');
        if($info){
            $getinfo = $info->getInfo();
            $url = $this->config['pathFormat'].$info->getSaveName();
            $url = str_replace('\\','/',$url);
            $res = array(
                "state" => "SUCCESS",          //上传状态，上传成功时必须返回"SUCCESS"
                "url" => $url,            //返回的地址
                "title" => $info->getFilename(),          //新文件名
                "original" => $getinfo['name'],       //原始文件名
                "type" => $getinfo['type'],            //文件类型
                "size" => $getinfo['size'],           //文件大小
            );
        }else{
            // 上传失败获取错误信息
            //echo $file->getError();
            $res['state'] = "error";
        }
        return json_encode($res);
    }

    public function uploadfilebase64(){
        return "uploadfilebase64";
    }

    public function listfile($allowFiles,$listSize,$path){
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;

        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
        $files = self::getfiles($path, $allowFiles);
        if (!count($files)) {
            return json_encode(array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            ));
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}

        /* 返回数据 */
        $result = json_encode(array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ));

        return $result;
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private function getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    self::getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

    public function saveremote(){
        /* 抓取远程图片 */
        $list = array();
        if (isset($_POST[$this->fieldName])) {
            $source = $_POST[$this->fieldName];
        } else {
            $source = $_GET[$this->fieldName];
        }
        foreach ($source as $imgUrl) {
            $imgUrl = htmlspecialchars($imgUrl);
            $imgUrl = str_replace("&amp;", "&", $imgUrl);

            //http开头验证
            if (strpos($imgUrl, "http") !== 0) {
                $res['state'] = "ERROR_HTTP_LINK";
                return json_encode($res);
            }

            preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
            $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

            // 判断是否是合法 url
            if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
                $res['state'] = "INVALID_URL";
                return json_encode($res);
            }

            preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
            $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

            // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
            $ip = gethostbyname($host_without_protocol);
            // 判断是否是私有 ip
            if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
                $res['state'] = "INVALID_IP";
                return json_encode($res);
            }

            //获取请求头并检测死链
            $heads = get_headers($imgUrl, 1);
            if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
                $res['state'] = "ERROR_DEAD_LINK";
                return json_encode($res);
            }
            //格式验证(扩展名验证和Content-Type验证)
            $fileType = strtolower(strrchr($imgUrl, '.'));
            if (!in_array($fileType, $config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
                $res['state'] = "ERROR_HTTP_CONTENTTYPE";
                return json_encode($res);
            }

            //打开输出缓冲区并获取远程图片
            ob_start();
            $context = stream_context_create(
                array('http' => array(
                    'follow_location' => false // don't follow redirects
                ))
            );
            readfile($imgUrl, false, $context);
            $img = ob_get_contents();
            ob_end_clean();
            preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);


            $this->oriName = $m ? $m[1]:"";
            $this->fileSize = strlen($img);
            $this->fileType = $this->getFileExt();
            $this->fullName = $this->getFullName();
            $this->filePath = $this->getFilePath();
            $this->fileName = $this->getFileName();

            $dirname = dirname($this->filePath);

            //检查文件大小是否超出限制
            if (strlen($img) > $config['maxSize']) {
                $res['state'] = "too large";
                return json_encode($res);
            }

            //创建目录失败
            if (!file_exists($dirname) && !mkdir($dirname, 0755, true)) {
                $res['state'] = "ERROR_CREATE_DIR";
                return json_encode($res);
            } else if (!is_writeable($dirname)) {
                $res['state'] = "ERROR_DIR_NOT_WRITEABLE";
                return json_encode($res);
            }

            //移动文件
            if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) { //移动失败
                $res['state'] = "ERROR_WRITE_CONTENT";
            } else { //移动成功
                $res['state'] = "SUCCESS";
            }

            array_push($list, array(
                "state" => $res["state"],
                "url" => $this->fullName,
                "size" => $this->fileSize,
                "title" => htmlspecialchars($this->fileName),
                "original" => htmlspecialchars($this->oriName),
                "source" => htmlspecialchars($imgUrl)
            ));
        }

        /* 返回抓取数据 */
        return json_encode(array(
            'state'=> count($list) ? 'SUCCESS':'ERROR',
            'list'=> $list
        ));

    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        return strtolower(strrchr($this->oriName, '.'));
    }

    /**
     * 重命名文件
     * @return string
     */
    private function getFullName()
    {
        //替换日期事件
        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $this->config["pathFormat"];
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);

        //过滤文件名的非法自负,并替换文件名
        $oriName = substr($this->oriName, 0, strrpos($this->oriName, '.'));
        $oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $oriName);
        $format = str_replace("{filename}", $oriName, $format);

        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }

        $ext = $this->getFileExt();
        return $format . $ext;
    }
    /**
     * 获取文件名
     * @return string
     */
    private function getFileName () {
        return substr($this->filePath, strrpos($this->filePath, '/') + 1);
    }

    /**
     * 获取文件完整路径
     * @return string
     */
    private function getFilePath()
    {
        $fullname = $this->fullName;
        $rootPath = $_SERVER['DOCUMENT_ROOT'];

        if (substr($fullname, 0, 1) != '/') {
            $fullname = '/' . $fullname;
        }

        return $rootPath . $fullname;
    }




}
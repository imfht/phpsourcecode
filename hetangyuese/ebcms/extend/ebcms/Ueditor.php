<?php
/**
 * Ueditor插件
 * @author Nintendov
 */
namespace ebcms;
class Ueditor
{

    //public $uid;//要操作的用户id 如有登录需要则去掉注释

    private $output;//要输出的数据

    private $st;

    private $rootpath = '/upload';

    private $domainroot;

    public function __construct($custom_config, $uid = '')
    {

        //uid 为空则导入当前会话uid
        $this->uid = $uid;

        $this -> domainroot = request() -> root(true);

        //导入设置
        $config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./third/ueditor/php/config.json")), true);
        $config = array_merge($config, $custom_config);

        $action = htmlspecialchars($_GET['action']);
        switch ($action) {
            case 'config':
                $result = $config;
                break;

            case 'uploadimage':
                $conf = array(
                    "pathFormat" => $config['imagePathFormat'],
                    "maxSize" => $config['imageMaxSize'],
                    "allowFiles" => $config['imageAllowFiles']
                );
                $fieldName = $config['imageFieldName'];
                $result = $this->uploadFile($conf, $fieldName);
                break;

            case 'uploadscrawl':
                $conf = array(
                    "pathFormat" => $config['scrawlPathFormat'],
                    "maxSize" => $config['scrawlMaxSize'],
                    "allowFiles" => $config['imageAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $config['scrawlFieldName'];
                $result = $this->uploadBase64($conf, $fieldName);
                break;

            case 'uploadvideo':
                $conf = array(
                    "pathFormat" => $config['videoPathFormat'],
                    "maxSize" => $config['videoMaxSize'],
                    "allowFiles" => $config['videoAllowFiles']
                );
                $fieldName = $config['videoFieldName'];
                $result = $this->uploadFile($conf, $fieldName);
                break;

            case 'uploadfile':
                // default:
                $conf = array(
                    "pathFormat" => $config['filePathFormat'],
                    "maxSize" => $config['fileMaxSize'],
                    "allowFiles" => $config['fileAllowFiles']
                );
                $fieldName = $config['fileFieldName'];
                $result = $this->uploadFile($conf, $fieldName);
                break;

            case 'listfile':
                $conf = array(
                    'allowFiles' => $config['fileManagerAllowFiles'],
                    'listSize' => $config['fileManagerListSize'],
                    'path' => $config['fileManagerListPath'],
                );
                $result = $this->listFile($conf);
                break;

            case 'listimage':
                $conf = array(
                    'allowFiles' => $config['imageManagerAllowFiles'],
                    'listSize' => $config['imageManagerListSize'],
                    'path' => $config['imageManagerListPath'],
                );
                $result = $this->listFile($conf);
                break;

            case 'catchimage':
                $conf = array(
                    "pathFormat" => $config['catcherPathFormat'],
                    "maxSize" => $config['catcherMaxSize'],
                    "allowFiles" => $config['catcherAllowFiles'],
                    "oriName" => "remote.png"
                );
                $fieldName = $config['catcherFieldName'];
                $result = $this->saveRemote($conf, $fieldName);
                break;

            default:
                $result = array(
                    'state' => 'wrong require'
                );
                break;

        }

        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                $this->output = htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                $this->output = array(
                    'state' => 'callback参数不合法'
                );
            }
        } else {
            $this->output = $result;
        }
    }


    /**
     *
     * 输出结果
     * @param data 数组数据
     * @return 组合后json格式的结果
     */
    public function output()
    {
        return $this->output;
    }

    /**
     * 上传文件方法
     *
     */
    private function uploadFile($config, $fieldName)
    {
        $file = \think\Request::instance()->file($fieldName);
        $path = $this->getFullPath($config['pathFormat']);
        $info = $file->move('.' . $this->rootpath . $path);
        if (false === $info) {
            $data = array(
                "state" => '上传失败！',
            );
        } else {
            $data = array(
                'state' => "SUCCESS",
                'url' => $this->domainroot . substr(str_replace('\\', '/', $info->getPath() . '/' . $info->getBasename()), 1),
                'title' => $info->getBasename(),
                'original' => $info->getBasename(),
                'type' => '.' . $info->getExtension(),
                'size' => $info->getSize(),
            );
        }
        return $data;
    }

    /**
     *
     * Enter description here ...
     */
    private function uploadBase64($config, $fieldName)
    {
        $data = array();

        $base64Data = $_POST[$fieldName];
        $img = base64_decode($base64Data);
        $path = $this->getFullPath($config['pathFormat']);
        if (strlen($img) > $config['maxSize']) {
            $data['states'] = 'too large';
            return $data;
        }

        $rootpath = $this->rootpath;

        //替换随机字符串
        $imgname = uniqid() . '.png';
        $filename = $path . $imgname;
        if (!$this->checkdir(dirname('.' . $rootpath . $path))) {
            $data = array(
                'state' => '目录 .' . $rootpath . ' 创建失败！',
            );
            return $data;
        }
        if (file_put_contents('.' . $rootpath . $filename, $img)) {
            $data = array(
                'state' => 'SUCCESS',
                'url' => $this->domainroot . $this->rootpath . $filename,
                'title' => $imgname,
                'original' => 'scrawl.png',
                'type' => '.png',
                'size' => strlen($img),

            );
        } else {
            $data = array(
                'state' => 'cant write',
            );
        }
        return $data;
    }

    /**
     * 列出文件夹下所有文件，如果是目录则向下
     */
    private function listFile($config)
    {
        $allowFiles = substr(str_replace(".", "|", join("", $config['allowFiles'])), 1);
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $config['listSize'];
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;

        $path = $config['path'];
        $files = $this->_listFile($path, $allowFiles);
        if (!count($files)) {
            return array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            );
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}

        /* 返回数据 */
        return array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        );
    }

    private function _listFile($path, $allowFiles = 'all')
    {
        $path = '.' . $this->rootpath . $path;
        return $this->_getList($path, $allowFiles);
    }

    private function _getList($path, $allowFiles = 'all', &$files = array())
    {
        if (!is_dir($path)) return null;
        if (substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->_getList($path2, $allowFiles, $files);
                } else {
                    if ($allowFiles != 'all') {
                        if (preg_match("/\.(" . $allowFiles . ")$/i", $file)) {
                            $files[] = array(
                                'url' => $this->domainroot . $this->rootpath . substr($path2, strlen('.' . $this->rootpath)),
                                'mtime' => filemtime($path2)
                            );
                        }
                    } else {
                        $files[] = array(
                            'url' => $this->domainroot . $this->rootpath . substr($path2, strlen('.' . $this->rootpath)),
                            'mtime' => filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

    /**
     *
     * Enter description here ...
     */
    private function saveRemote($config, $fieldName)
    {
        $list = array();
        if (isset($_POST[$fieldName])) {
            $source = $_POST[$fieldName];
        } else {
            $source = $_GET[$fieldName];
        }
        foreach ($source as $imgUrl) {

            $imgUrl = htmlspecialchars($imgUrl);
            $imgUrl = str_replace("&amp;", "&", $imgUrl);

            //http开头验证
            if (strpos($imgUrl, "http") !== 0) {
                return array('state' => '不是http链接');
            }
            //格式验证(扩展名验证和Content-Type验证)
            $fileType = strtolower(strrchr($imgUrl, '.'));
            if (!in_array($fileType, $config['allowFiles'])) {
                return array("state" => "错误文件格式");
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

            $path = $this->getFullPath($config['pathFormat']);
            if (strlen($img) > $config['maxSize']) {
                $data['states'] = 'too large';
                return $data;
            }

            $rootpath = $this->rootpath;

            $imgname = uniqid() . '.png';
            $filename = $path . $imgname;

            $oriName = $m ? $m[1] : "";
            if (!$this->checkdir(dirname('.' . $rootpath . $path))) {
                $data = array(
                    'state' => '目录 .' . $rootpath . ' 创建失败！',
                );
                return $data;
            }

            if (file_put_contents('.' . $rootpath . $filename, $img)) {
                array_push($list, array(
                    "state" => 'SUCCESS',
                    "url" => $this->domainroot . $this->rootpath . $filename,
                    "size" => strlen($img),
                    "title" => $imgname,
                    "original" => $oriName,
                    "source" => htmlspecialchars($imgUrl)
                ));
            } else {
                array_push($list, array('state' => '文件写入失败'));
            }
        }

        /* 返回抓取数据 */
        return array(
            'state' => count($list) ? 'SUCCESS' : 'ERROR',
            'list' => $list
        );
    }

    /**
     * 规则替换命名文件
     * @param $path
     * @return string
     */
    private function getFullPath($path)
    {
        //替换日期事件
        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $path;
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);
        $format = str_replace("{uid}", $this->uid, $format);

        $randNum = rand(1, 100000) . rand(1, 100000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }

        return $format;
    }

    private function checkdir($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                return false;
            }
        }
        return true;
    }

    private function format_exts($exts)
    {
        $data = array();
        foreach ($exts as $key => $value) {
            $data[] = ltrim($value, '.');
        }
        return $data;
    }

}
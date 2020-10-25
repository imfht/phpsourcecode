<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo;


/**
 * 文件上传类
 *
 * Class UploadFiles
 * @package Timo
 */
class UploadFiles
{
    private $config = [
        'maxSize' => 8388608, //文件大小限制（默认：8M）
        'allowExts' => [], //允许上传的文件后缀，留空则不做限制，不带点
        'allowTypes' => [], //允许上传的文件类型，留空不作检查
        'subDir' => false,//启用子目录保存文件
        'subDirType' => 'hash', //子目录创建方式，hash\date两种
        'dateFormat' => 'Y/m/d', //按日期保存的格式
        'hashLevel' => 1, //hash的目录层次
        'savePath' => '', //上传文件的保存路径
        'replace' => false, //替换同名文件
        'rename' => true,//是否生成唯一文件名
    ];

    /**
     * 上传成功的信息
     *
     * @var array
     */
    private $successInfo;

    /**
     * 上传失败的信息
     *
     * @var string
     */
    private $errorInfo = '';

    /**
     * 获取配置
     *
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }

    /**
     * 设置配置
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 是否存在配置项
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    /**
     * 构造函数，合并配置项
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        is_array($config) && $this->config = array_merge($this->config, $config);
    }

    /**
     * 上传文件
     *
     * @param string $input_file_name input file表单名称
     * @param null $savePath 文件保存绝对路径，如：/data/TimoPHP/www/static/uploads/avatar/000/001/0001/
     * @param bool $is_save 是否保存图片，false不保存图片值检测
     * @return bool
     */
    public function upload($input_file_name, $savePath = null, $is_save = true)
    {
        $fileInfo = [];
        $isUpload = false;

        is_null($savePath) && $savePath = $this->config['savePath'];

        if (!isset($_FILES[$input_file_name])) {
            $this->errorInfo = '没有指定的上传文件';
            return false;
        }

        //获取上传的文件信息
        $files = $this->formatFiles($_FILES[$input_file_name]);
        foreach ($files as $key => $file) {
            if (!empty($file['name'])) {
                $file['extension'] = pathinfo($file['name'], PATHINFO_EXTENSION);
                $file['savepath'] = $savePath;

                //保存的文件名
                $file['savename'] = $this->getSaveName($savePath, $file);

                //创建目录
                if (is_dir($file['savepath'])) {
                    if (!is_writable($file['savepath'])) {
                        $this->errorInfo = "上传目录{$savePath}不可写";
                        return false;
                    }
                } else {
                    if (!mkdir($file['savepath'], 0700, true)) {
                        $this->errorInfo = "上传目录{$savePath}不可写";
                        return false;
                    }
                }

                //自动查检附件
                if (!$this->secureCheck($file)) {
                    return false;
                }

                //保存上传文件
                if ($is_save && !$this->save($file)) {
                    return false;
                }
                $fileInfo[] = $file;
                $isUpload = true;
            }
        }
        if ($isUpload) {
            $this->successInfo = $fileInfo;
            return true;
        } else {
            $this->errorInfo = '没有选择上传文件';
            return false;
        }
    }

    /**
     * 根据上传文件命名规则取得保存文件名
     *
     * @param $savepath
     * @param $file
     * @return string
     */
    protected function getSaveName($savepath, $file)
    {
        $saveName = $this->config['rename'] ? uniqid() . mt_rand(1000, 9999) . '.' . $file['extension'] : $file['name'];
        if ($this->config['subDir']) {
            //使用子目录保存文件
            switch ($this->config['subDirType']) {
                case 'date':
                    $dir = date($this->config['dateFormat'], NOW_TIME) . DIRECTORY_SEPARATOR;
                    break;
                case 'hash':
                default:
                    $name = md5($saveName);
                    $dir = '';
                    for ($i = 0; $i < $this->config['hashLevel']; $i++) {
                        $dir .= $name{$i} . DIRECTORY_SEPARATOR;
                    }
                    break;
            }
            if (!is_dir($savepath . $dir)) {
                mkdir($savepath . $dir, 0700, true);
            }
            $saveName = $dir . $saveName;
        }
        return $saveName;
    }

    /**
     * 格式化多文件数组
     *
     * @param $file array 要上传的文件，$_FILES['cover']
     * @return array
     */
    protected function formatFiles($file)
    {
        $fileArray = [];
        if (is_array($file['name'])) {
            $keys = array_keys($file);
            $count = count($file['name']);
            for ($i = 0; $i < $count; $i++) {
                foreach ($keys as $_key) {
                    $fileArray[$i][$_key] = $file[$_key][$i];
                }
            }
        } else {
            $fileArray[] = $file;
        }
        return $fileArray;
    }

    /**
     * 保存
     *
     * @param array $file
     *
     * @return bool
     */
    protected function save($file)
    {
        $filename = $file['savepath'] . $file['savename'];
        if (!$this->config['replace'] && is_file($filename)) { //不覆盖同名文件
            $this->errorInfo = "文件已经存在{$filename}";
            return false;
        }
        //如果是图片，检查格式
        if (in_array(strtolower($file['extension']), ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])
            && false === getimagesize($file['tmp_name'])
        ) {
            $this->errorInfo = '非法图像文件';
            return false;
        }
        if (!move_uploaded_file($file['tmp_name'], $filename)) {
            $this->errorInfo = '文件上传错误!';
            return false;
        }
        return true;
    }

    /**
     * 检查上传的文件有没上传成功是否合法
     *
     * @param array $file 上传的单个文件
     *
     * @return bool
     */
    protected function secureCheck($file)
    {
        //文件上传失败，检查错误码
        if ($file['error'] != 0) {
            switch ($file['error']) {
                case 1:
                    $this->errorInfo = '上传的文件大小超过了 php.ini 中 upload_max_filesize 选项限制的值';
                    break;
                case 2:
                    $this->errorInfo = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
                    break;
                case 3:
                    $this->errorInfo = '文件只有部分被上传';
                    break;
                case 4:
                    $this->errorInfo = '没有文件被上传';
                    break;
                case 6:
                    $this->errorInfo = '找不到临时文件夹';
                    break;
                case 7:
                    $this->errorInfo = '文件写入失败';
                    break;
                default:
                    $this->errorInfo = '未知上传错误！';
            }
            return false;
        }
        //文件上传成功，进行自定义检查
        if ($file['size'] > $this->config['maxSize']) {
            $this->errorInfo = '上传文件大小不符';
            return false;
        }
        //检查文件Mime类型
        if (!$this->checkType($file['type'])) {
            $this->errorInfo = '上传文件mime类型允许';
            return false;
        }
        //检查文件类型
        if (!$this->checkExt($file['extension'])) {
            $this->errorInfo = '上传文件类型不允许';
            return false;
        }
        //检查是否合法上传
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->errorInfo = '非法的上传文件！';
            return false;
        }
        return true;
    }

    /**
     * 查检文件的mime类型是否合法
     *
     * @param string $type
     *
     * @return bool
     */
    protected function checkType($type)
    {
        if (!empty($this->allowTypes)) {
            return in_array(strtolower($type), $this->allowTypes);
        }
        return true;
    }

    /**
     * 检查上传的文件后缀是否合法
     *
     * @param string $ext
     *
     * @return bool
     */
    protected function checkExt($ext)
    {
        if (!empty($this->allowExts)) {
            return in_array(strtolower($ext), $this->allowExts, true);
        }
        return true;
    }

    /**
     * 取得最后一次错误信息
     *
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->errorInfo;
    }

    /**
     * 取得上传文件的信息
     *
     * @return array
     */
    public function getSuccessInfo()
    {
        return $this->successInfo;
    }
}

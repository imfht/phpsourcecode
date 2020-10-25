<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------

/**
 * 上传处理类
 * @package     tools_class
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
class Upload
{

    //上传类型
    public $ext = array();
    //上传文件大小
    public $size;
    //上传路径
    public $path;
    //错误信息
    public $error;
    //上传成功文件信息
    public $uploadedFile = array();

    /**
     * 构造函数
     * @param string $path 上传路径
     * @param array $ext 允许的文件类型,传入数组如array('jpg','jpeg','png','doc')
     * @param array $size 允许上传大小,如array('jpg'=>200000,'rar'=>'39999') 如果不设置系统会依据配置项C("UPLOAD_EXT_SIZE")值
     */
    public function __construct($path = null, $ext = array(), $size = null)
    {
        $path = $path ? $path : C("UPLOAD_PATH"); //上传路径
        $path = rtrim(str_replace('\\', '/', $path), '/') . '/';
        $this->path = str_replace(ROOT_PATH, '', $path);
        //上传类型
        $ext = $ext ? $ext : C("UPLOAD_ALLOW_TYPE");
        $this->ext = array_change_value_case($ext);
        //允许大小
        $this->size = $size ? $size : C("UPLOAD_ALLOW_SIZE");
    }


    /**
     * 将$_FILES中的文件上传到服务器
     * 可以只上传$_FILES中的一个文件
     * @param null $fieldName 上传的图片name名
     * @return array|bool
     * <code>
     * 示例1：
     * $upload= new Upload();
     * $upload->upload();
     * 示例2:
     * $upload= new Upload();
     * $upload->upload("pic");
     * </code>
     */
    public function upload($fieldName = null)
    {
        if (!$this->checkDir($this->path)) {
            $this->error = $this->path . '图片上传目录创建失败或不可写';
            return false;
        }
        $files = $this->format($fieldName);
        //验证文件
        if (!empty($files)) {
            foreach ($files as $v) {
                $info = pathinfo($v ['name']);
                $v ["ext"] = isset($info ["extension"]) ? $info['extension'] : '';
                $v['filename'] = isset($info['filename']) ? $info['filename'] : '';

                if (!$this->checkFile($v)) {
                    continue;
                }
                $uploadedFile = $this->save($v);
                if ($uploadedFile) {
                    $this->uploadedFile [] = $uploadedFile;
                }
            }
        }
        return $this->uploadedFile;
    }

    /**
     * 储存文件
     * @param string $file 储存的文件
     * @return boolean
     */
    private function save($file)
    {
        $fileName = mt_rand(1, 9999) . time() . "." . $file['ext'];
        $filePath = $this->path . $fileName;
        if (!move_uploaded_file($file ['tmp_name'], $filePath) && is_file($filePath)) {
            $this->error('移动临时文件失败');
            return false;
        }
        $_info =pathinfo($filePath);
        $arr = array();
        $arr['path'] = $filePath;
        $arr['uptime'] = time();
        $arr['fieldname'] = $file['fieldname'];
        $arr['basename'] = $_info['basename'];
        $arr['filename'] = $_info['filename']; //新文件名
        $arr['name'] = $file['filename']; //旧文件名
        $arr['size'] = $file['size'];
        $arr['ext'] = $file['ext'];
        $arr['dir'] = $this->path;
        $arr['url'] = __ROOT__ . '/' . str_ireplace(ROOT_PATH, '', $filePath);
        $arr['image'] = getimagesize($filePath) ? 1 : 0;
        return $arr;
    }

    //将上传文件整理为标准数组
    private function format($fieldName)
    {
        if ($fieldName == null) {
            $files = $_FILES;
        } else if (isset($_FILES[$fieldName])) {
            $files[$fieldName] = $_FILES[$fieldName];
        }
        if (!isset($files)) {
            $this->error = '没有任何文件上传';
            return false;
        }
        $info = array();
        $n = 0;
        foreach ($files as $name => $v) {
            if (is_array($v ['name'])) {
                $count = count($v ['name']);
                for ($i = 0; $i < $count; $i++) {
                    foreach ($v as $m => $k) {
                        $info [$n] [$m] = $k [$i];
                    }
                    $info [$n] ['fieldname'] = $name; //字段名
                    $n++;
                }
            } else {
                $info [$n] = $v;
                $info [$n] ['fieldname'] = $name; //字段名
                $n++;
            }
        }
        return $info;
    }

    /**
     * 验证目录
     * @param string $path 目录
     * @return bool
     */
    private function checkDir($path)
    {
        return Dir::create($path) && is_dir($path) ? true : false;
    }

    private function checkFile($file)
    {
        if ($file ['error'] != 0) {
            $this->error($file ['error']);
            return false;
        }
        $ext = strtolower($file ['ext']);
        if (!in_array($ext, $this->ext)) {
            $this->error = '文件类型不允许';
            return false;
        }
        if (strstr(strtolower($file['type']), "image") && !getimagesize($file['tmp_name'])) {
            $this->error = '上传内容不是一个合法图片';
            return false;
        }
        if ($file ['size'] > $this->size) {
            $this->error = '上传文件大于' . get_size($this->size);
            return false;
        }

        if (!is_uploaded_file($file ['tmp_name'])) {
            $this->error = '非法文件';
            return false;
        }
        return true;
    }

    private function error($error)
    {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE :
                $this->error = '上传文件超过PHP.INI配置文件允许的大小';
                break;
            case UPLOAD_ERR_FORM_SIZE :
                $this->error = '文件超过表单限制大小';
                break;
            case UPLOAD_ERR_PARTIAL :
                $this->error = '文件只上有部分上传';
                break;
            case UPLOAD_ERR_NO_FILE :
                $this->error = '没有上传文件';
                break;
            case UPLOAD_ERR_NO_TMP_DIR :
                $this->error = '没有上传临时文件夹';
                break;
            case UPLOAD_ERR_CANT_WRITE :
                $this->error = '写入临时文件夹出错';
                break;
        }
    }

    /**
     * 返回上传时发生的错误原因
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
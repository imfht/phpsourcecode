<?php

namespace app\admin\api;

use think\Model;
use think\Config;
use think\Request;

/**
 * Description of file
 * 文件模型
 * @author static7
 */
class File extends Model {

    // 常用文件格式（包括图片、文件、音视频）
    private $ext = [
        'ext' => 'zip,7z,tar.gz,rar,xls,doc,ppt,xlsx,docx,pptx,rm,rmvb,wmv,avi,mp4,mp3,3gp,mkv,gif,jpg,jpeg,bmp,png,swf,fla,flv'
    ];

    /**
     * 文件上传
     * @param  array  $file_name   要上传的文件名称
     * @param  array  $tmp_config  临时上传配置
     * @param  string $driver  上传驱动名称（后期完善）
     * @return array           文件上传成功后的信息
     * @author staitc7 <static7@qq.com>

     */
    public function upload($file_name = null, $tmp_config = null, $driver = 'local') {
        $config = empty($tmp_config) ? Config::get('file_path') : $tmp_config;
        $Request = Request::instance();
        $file = $Request->file($file_name);
        if (!$file->check(Config::get('file_upload_restrict'))) {
            return $file->getError(); // 上传失败获取错误信息 
        }
        if (is_array($file)) {
            $data = $this->arrayProcess($file, $config, $driver);
        } else {
            $data = $this->oneProcess($file, $config, $driver);
        }
        return $data;
    }

    /**
     * 单个文件处理
     * @param array $file 处理的数据
     * @param  array  $config  上传配置
     * @param  string $driver  上传名称（后期完善）
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    private function oneProcess($file, $config, $driver) {
        $info = $file->rule('uniqid')->move(getcwd() . $config);
        if (!$info) {
            return $file->getError(); // 上传失败获取错误信息
        }

        $data['md5'] = md5($info->getInfo()['tmp_name']);
        $data['sha1'] = sha1($info->getInfo()['tmp_name']);
        $id = $this::where(['md5' => $data['md5'], 'sha1' => $data['sha1']])->value('id');
        if (empty($id)) {
            $data['savename'] = $info->getfilename();
            $data['name'] = $info->getInfo()['name'];
            $data['mime'] = $info->getInfo()['type'];
            $data['size'] = $info->getInfo()['size'];
            $data['ext'] = $info->getExtension();
            $data['savepath'] = $config . $info->getBasename();
            $data['create_time'] = $info->getATime();
            $object = $this::create($data);
            return $object ? $object->toArray()['id'] : 0;
        }
        return (int) $id;
    }

    /**
     * 数组处理文件
     * @param array $file 处理的数据
     * @param  array  $config  上传配置
     * @param  string $driver  上传名称（后期完善）
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    private function arrayProcess($file, $config, $driver) {
        $ids = [];
        foreach ($file as $k => $v) {
            $info = $v->rule('uniqid')->move(getcwd() . $config);
            if (!$info) {
                return $file->getError(); // 上传失败获取错误信息
            }
            $data['md5'] = md5($info->getInfo()['tmp_name']);
            $data['sha1'] = sha1($info->getInfo()['tmp_name']);
            $id = $this::where(['md5' => $data['md5'], 'sha1' => $data['sha1']])->value('id');
            if (empty($id)) {
                $data['savename'] = $info->getfilename();
                $data['name'] = $info->getInfo()['name'];
                $data['mime'] = $info->getInfo()['type'];
                $data['size'] = $info->getInfo()['size'];
                $data['ext'] = $info->getExtension();
                $data['savepath'] = $config . $info->getBasename();
                $data['create_time'] = $info->getATime();
                $object = $this::create($data);
                $ids[] = $object ? $object->toArray()['id'] : 0;
            } else {
                $ids[] = $id;
            }
        }
        return empty($ids) ? 0 : $ids;
    }

    /**
     * @param  string  $file_name   要上传的文件名称
     * @param  array  $tmp_config  临时上传配置
     * @return array           文件上传成功后的信息
     * @author staitc7 <static7@qq.com>
     */
    public function editorUpload($file_name = null, $tmp_config = null) {
        $config = empty($tmp_config) ? Config::get('file_path') : $tmp_config;
        $Request = Request::instance();
        $file = $Request->file($file_name);
        if (!$file->check($this->ext)) {
            return $this->error = $file->getError(); // 上传失败获取错误信息 
        }
        $info = $file->rule('uniqid')->move(getcwd() . $config);
        if (!$info) {
            return $this->error = $file->getError(); // 上传失败获取错误信息
        }
        return $config . $info->getSaveName();
    }

}

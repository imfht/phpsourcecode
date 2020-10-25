<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\logic;


use app\common\logic\Common as LogicCommon;

/**
 * 文件处理逻辑
 */
class File extends Common
{


    protected $fileext;
    protected $filesize;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        //  parent::_initialize();
        $this->fileext  = webconfig('upload_file_ext');
        $this->filesize = webconfig('upload_file_size');

        $this->pictureext  = webconfig('upload_picture_ext');
        $this->picturesize = webconfig('upload_picture_size');

        //这里需要判断当前的用户属于哪个组，这个组是否有特殊的附件上传限制，大小取最小的，后缀取共有值


    }

    /**
     * 下载方法
     */
    public function download($url, $name, $local)
    {

        $down = new \extend\Http();

        if ($local == 1) {


            $down->download($url, $name);


        } else {


        }


    }

    /**
     * 获取上传文件信息
     */
    public function getFileInfo($where = [], $field = true)
    {

        return $this->setname('file')->getDataInfo($where, $field);
    }

    /**
     * 获取上传图片信息
     */
    public function getPictureInfo($where = [], $field = true)
    {

        return $this->setname('picture')->getDataInfo($where, $field);
    }

    /**
     * 图片上传
     * small,medium,big
     */
    public function pictureUpload($name = 'file')
    {

        $object_info = request()->file($name);


        $sha1 = $object_info->hash();
        $md5  = $object_info->hash('md5');

        $picture_info = $this->getPictureInfo(['sha1' => $sha1], 'id,name,path,sha1');

        if (!empty($picture_info)) {

            $picture_info['headpath']     = WEB_PATH_PICTURE . $picture_info['path'];
            $picture_info['userheadpath'] = SYS_DSS . 'uploads' . SYS_DSS . 'picture' . SYS_DSS . $picture_info['path'];
            $picture_info['hasupload']    = 1;


            return $picture_info;
        }

        $object = $object_info->validate(['size' => $this->picturesize, 'ext' => $this->pictureext])->move(PATH_PICTURE);


        //此处读取配置文件，看可以传什么文件
        if ($object) {

            $save_name = $object->getSaveName();

            $save_path = PATH_PICTURE . $save_name;

            $picture_dir_name = substr($save_name, 0, strrpos($save_name, DS));

            $filename = $object->getFilename();

            $data = ['name' => $filename, 'path' => $picture_dir_name . SYS_DSS . $filename, 'sha1' => $sha1, 'md5' => $md5];

            $result = $this->setname('picture')->dataAdd($data, false);

            session('last_uploadid', $result);

            $data['headpath'] = WEB_PATH_PICTURE . $picture_dir_name . SYS_DSS . $filename;

            $data['userheadpath'] = SYS_DSS . 'uploads' . SYS_DSS . 'picture' . SYS_DSS . $picture_dir_name . SYS_DSS . $filename;


            if ($result[0] == 'success') : $data['id'] = $result[3];
                return $data; endif;

            return false;

        } else {

            return ['errormsg' => $object_info->getError(), 'code' => 0];

        }


    }

    /**
     * 文件上传
     * small,medium,big
     */
    public function fileUpload($name = 'file')
    {


        $object_info = request()->file($name);

        $sha1 = $object_info->hash();
        $md5  = $object_info->hash('md5');


        $file_info = $this->getFileInfo(['sha1' => $sha1], 'id,name,path,sha1');


        if (!empty($file_info)) {

            $file_info['headpath'] = WEB_PATH_FILE . $file_info['savepath'];
            return $file_info;
        }

        $object = $object_info->validate(['size' => $this->filesize, 'ext' => $this->fileext])->move(PATH_FILE);

        //此处读取配置文件，看可以传什么文件

        if ($object) {
            $ext       = $object->getExtension();
            $save_name = $object->getSaveName();

            $save_path = PATH_FILE . $save_name;

            $file_dir_name = substr($save_name, 0, strrpos($save_name, DS));

            $filename = $object->getFilename();

            $fileinfo = $object->getInfo();

            $filenamearr = explode('.' . $ext, $fileinfo['name']);

            $data = ['name' => $filenamearr[0], 'ext' => $ext, 'size' => $fileinfo['size'], 'mime' => $fileinfo['type'], 'savepath' => $file_dir_name . SYS_DSS . $filename, 'savename' => $filename, 'sha1' => $sha1, 'md5' => $md5];

            $result = $this->setname('file')->dataAdd($data, false);


            $data['headpath'] = WEB_PATH_FILE . $file_dir_name . SYS_DSS . $filename;


            if ($result[0] == 'success') : $data['id'] = $result[3];
                return $data; endif;

            return false;
        } else {

            return ['errormsg' => $object_info->getError(), 'code' => 0];

        }

    }


}

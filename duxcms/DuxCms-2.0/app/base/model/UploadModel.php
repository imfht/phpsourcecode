<?php
namespace app\base\model;
use app\base\model\BaseModel;
/**
 * 上传模块
 */
class UploadModel extends BaseModel {

    /**
     * 上传数据
     * @param array $config 上传配置信息可选
     * @return array 文件信息
     */
    public function upload($config = array())
    {
        $baseConfig = load_config(CONFIG_PATH . 'upload.php');
        $config = array_merge((array)$baseConfig, (array)$config);
        if(empty($config['DIR_NAME'])){
            $config['DIR_NAME'] = date('Y-m-d');
        }
        $path = UPLOAD_NAME . '/' . $config['DIR_NAME'] . '/';
        //上传
        $upload = new \framework\ext\UploadFile();
        $upload->savePath = ROOT_PATH . $path;
        $upload->allowExts = explode(',', $config['UPLOAD_EXTS']);
        $upload->maxSize = intval($config['UPLOAD_SIZE'])*1024*1024;
        $upload->saveRule = 'md5_file';
        if (!$upload->upload()) {
            $this->error = $upload->getErrorMsg();
            return false;
        }
        //上传信息
        $info = $upload->getUploadFileInfo();
        $info = current($info);
        //设置基本信息
        $file = $path . $info['savename'];
        $fileUrl = ROOT_URL . $file;
        $filePath = pathinfo($info['savename']);
        $fileName = $filePath['filename'];
        $fileTitle = pathinfo($info['name']);
        $fileTitle = $fileTitle['filename'];
        $fileExt = $info['extension'];
        //设置保存文件名(针对图片有效)
        if($config['SAVE_EXT']){
            $saveName = $fileName. '.' . $config['SAVE_EXT'];
        }else{
            $saveName = $info['savename'];
        }
        //处理图片数据
        $imgType = array('jpg','jpeg','png','gif','bmp');
        if(in_array(strtolower($fileExt), $imgType)){
            //设置图片驱动
            $image = new \app\base\util\ThinkImage();
            //设置缩图
            if($config['THUMB_STATUS']){
                $image->open(ROOT_PATH . $file);
                $thumbFile = $path.'thumb_'.$saveName;
                $status = $image->thumb($config['THUMB_WIDTH'], $config['THUMB_HEIGHT'], $config['THUMB_TYPE'])->save(ROOT_PATH . $thumbFile);
                if($status){
                    $file = $thumbFile;
                }
            }
            //设置水印
            if($config['WATER_STATUS']){
                $image->open(ROOT_PATH . $file);
                $wateFile = $path.'wate_'.$saveName;
                $status = $image->water(ROOT_PATH . 'public/watermark/'.$config['WATER_IMAGE'],$config['WATER_POSITION'])->save(ROOT_PATH . $wateFile);
                if($status){
                    $file = $wateFile;
                }
            }
        }
        //录入文件信息
        $data = array();
        $data['url'] = ROOT_URL . $file;
        $data['original'] = $fileUrl;
        $data['title'] = $fileTitle;
        $data['ext'] = $fileExt;
        $data['size'] = $info['size'];
        $data['time'] = time();
        return $data;
    }

}

<?php
namespace Modules\File\Library;

use Core\Config;
use Core\File;
use Modules\File\Models\File as Mfile;

class FileHandle
{
    public static function upload($params = array(),$fileKey=null)
    {
        /*
         * @max 最大尺寸
         * @min 最小尺寸
         * @type array 文件类型
         * @access 文件权限
         * @dir 文件保存路径
         * @watermarking 是否添加水印
         * @limit 文件数量
         * @value id|path
         * valueType string|array
         */
        global $di;
        $settings = Config::get('m.file.settings');
        $output = array('error' => array(), 'success' => array());
        $roles = $di->getShared('user')->roles;
        $min = array();
        $max = array();
        $type = array();
        $imageType = array(
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'jpeg' => 'image/jpeg',
        );
        foreach ($roles as $role) {
            if (isset($settings[$role . '_upload_size_min'])) {
                $min[] = $settings[$role . '_upload_size_min'];
            }
            if (isset($settings[$role . '_upload_size_max'])) {
                $max[] = $settings[$role . '_upload_size_max'];
            }
            if (isset($settings[$role . '_upload_size_type'])) {
                $type = array_merge($type, $settings[$role . '_upload_size_type']);
            }
        }
        $type = array_unique($type);
        if ($min) {
            $min = min($min);
        } else {
            $min = 1;
        }
        if ($max) {
            $max = max($max);
        } else {
            $max = 6000;
        }
        $default = array(
            'access' => '20',
            'type' => $type,
            'max' => $max,
            'min' => $min,
            'dir' => false,
            'watermark' => isset($settings['watermark']) ? $settings['watermark'] : 0,
            'limit' => 1,
        );
        $num = 0;
        $params = array_merge($default, $params);
        if ($params['max']) {
            $params['max'] = $params['max'] * 1024;
        }
        if ($params['min']) {
            $params['min'] = $params['min'] * 1024;
        }
        // Print the real file names and sizes
        foreach ($di->getShared('request')->getUploadedFiles(true) as $file) {
            if($fileKey != null){
                if(is_string($fileKey)){
                    if($file->getKey() != $fileKey){
                        continue;
                    }
                }elseif(is_array($fileKey)){
                    if(array_search($file->getKey(),$fileKey) === false){
                        continue;
                    }
                }
            }
            $fileName = $file->getName();
            $fileSize = $file->getSize();
            $fileNameInfo = explode('.', $fileName);
            $fileType = end($fileNameInfo);
            if ($num > $params['limit']) {
                $output['error'][] = array(
                    'fileName' => $fileName,
                    'flash' => '文件被忽略，超过单次上传文件数量上限：' . $params['num'],
                );
                continue;
            }
            if ($params['type']) {
                if (!in_array($fileType, $params['type'])) {
                    $output['error'][] = array(
                        'fileName' => $fileName,
                        'flash' => '文件类型不支持',
                    );
                    continue;
                }
            }
            if ($params['max']) {
                if ($fileSize > $params['max']) {
                    $output['error'][] = array(
                        'fileName' => $fileName,
                        'flash' => '文件过大',
                    );
                    continue;
                }
            }
            if ($params['min']) {
                if ($fileSize < $params['min']) {
                    $output['error'][] = array(
                        'fileName' => $fileName,
                        'flash' => '文件过小',
                    );
                    continue;
                }
            }
            //预留匿名上传逻辑
            if ($params['dir']) {
                $reDir = 'file/' . $params['dir'] . '/' . $fileType . date('/Y/m/d/');
            } else {
                $reDir = 'file/' . $fileType . date('/Y/m/d/');
            }
            $newFileName = md5($fileName) . '.' . $fileType;
            $modelPath = $reDir . $newFileName;
            $paramsAccess = str_split($params['access'], 1);
            switch ($paramsAccess[0]) {
                case '1':
                    //私有上传
                    $reDir = 'web/' . WEB_CODE . '/' . $reDir; //文件的相对地址
                    $path = ROOT_DIR . $reDir; //文件的绝对地址
                    $modelPath = $reDir;
                    break;
                case '2':
                    $path = WEB_DIR . $reDir;
                    $reDir = WEB_CODE . '/' . $reDir;
                    //共有上传
                    break;
            }
            //Print file details
            //echo $file->getName(), " ", $file->getSize(), "\n";

            //Move the file into the application
            if (!file_exists($path)) {
                @File::mkdir($reDir);
            }
            if (!is_writable($path) && file_exists($path)) {
                chmod($path, 0755);
            }
            if (file_exists($path)) {

                //echo $fileName;
                $fileAbName = $path . $newFileName;
                $db = $di->getShared('db');
                $db->begin();
                $fileModel = new Mfile();
                $fileModel->access = $params['access'];
                $fileModel->content_type = $fileType;
                $fileModel->uid = 0;
                $fileModel->path = $modelPath;
                $fileModel->name = $newFileName;
                $fileModel->created = time();
                $fileModel->changed = time();
                if ($fileModel->save()) {
                    if ($file->moveTo($fileAbName)) {
                        //$waterMark = new Watermask($fileAbName);
                        //$waterMark->output();
                        //unset($waterMark);
                        $num++;
                        $url = '';
                        if ($paramsAccess[0] == '1') {
                            $url = $di->getShared('url')->get(array(
                                'for' => 'privateFile',
                                'id' => $fileModel->id,
                            ));
                        } elseif ($paramsAccess[0] == '2') {
                            $url = '/' . $fileModel->path;
                        }
                        $output['success'][] = array(
                            'fileName' => $fileName,
                            'path' => $reDir,
                            'id' => $fileModel->id,
                            'url' => $url,
                            'newName' => $newFileName,
                            'flash' => '文件上传成功',
                        );
                        $db->commit();
                    } else {
                        $db->rollback();
                        $output['error'][] = array(
                            'fileName' => $fileName,
                            'flash' => '文件移动失败',
                        );
                    }
                } else {
                    $db->rollback();
                    $output['error'][] = array(
                        'fileName' => $fileName,
                        'flash' => '文件信息录入数据库失败',
                    );
                }
            }
        }
        return $output;
    }

    public static function delete()
    {

    }

    public static function editor()
    {

    }
}

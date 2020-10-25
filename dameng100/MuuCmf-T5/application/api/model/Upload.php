<?php
namespace app\api\model;

use think\Model;
use think\Db;

class Upload extends Model
{

    /**
     * 通用上传
     *
     * @param      <type>  $files   The files
     * @param      string  $type    The type
     * @param      array   $params  The parameters
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function upload($files, $type = "picture", $dirname = '', $uid = 0)
    {

        if($type=='picture'){
            $result = $this->picture($files, $dirname);
        }
        if($type=='file'){
            $result = $this->file($files, $dirname);
        }
        if($type=='avatar'){
            $result = $this->avatar($files, $dirname, $uid);
        }
        if($type=='base64'){
            $result = $this->base64($files, $dirname);
        }

        return $result;

    }

    /**
     * 图片上传
     *
     * @param      <type>         $files  The files
     *
     * @return     array|boolean  ( description_of_the_return_value )
     */
    private function picture($files, $dirname)
    {
        
        $config = config('upload.image');
        foreach($files as $file){
            if (empty($files)) {
                $this->error = $file->getError();
                return false;
            }
            //判断是否已经存在
            $sha1 = $file->hash();
            //处理已存在图片
            if($sha1){
                $pic_info = Db::name('Picture')->where(['sha1'=>$sha1])->find();
                if($pic_info){
                    $return['data'][] = $pic_info;
                    continue;
                }
            }

            //获取上传驱动
            $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
            $driver = check_driver_is_exist($driver);
            //构建返回数据
            
            if($driver == 'local'){
                $info = $file->validate(['size'=>$config['maxsize'],'ext'=>$config['mimetype']])->move($config['savepath']);
                if($info){
                    // 成功上传后 获取上传信息
                    $data['path'] = DS . 'uploads'  . DS . 'picture'  . DS . $info->getSaveName();
                    $data['path'] = str_replace("\\","/",$data['path']);
                    $data['md5'] = $info->md5();
                    $data['sha1'] = $info->sha1();
                }else{
                    $this->error = $file->getError();
                    return false;
                }
            }else{
                $data['md5'] = $file->hash('md5');
                $data['sha1'] = $file->hash('sha1');

                //调用驱动上传数据
                $res = $this->uploadDriver($driver, $file, $dirname);
                if(isset($res['savepath'])){
                    $data['path'] = $res['savepath']; 
               }else{
                    $this->error = $res;
                    return false;
               }
            }

            //写入数据库
            $data['create_time'] = time();
            $data['driver'] = $driver;
            $data['status'] = 1;
            $id = Db::name('Picture')->insertGetId($data);
            if($id){
                $data['id'] = $id;
                $data['path'] = pic($id);
                $return['data'][] = $data;  
            }
        }
        return $return['data'];
    }

    /**
     * 文件上传
     *
     * @param      <type>         $files  The files
     *
     * @return     array|boolean  ( description_of_the_return_value )
     */
    public function file($files, $dirname)
    {   
        $config = config('upload.file');
        
        foreach($files as $file){
            if (empty($files)) {
                $this->error = $file->getError();
                return false;
            }
            //判断是否已经存在附件
            $sha1 = $file->hash();
            //处理已存在文件
            if($sha1){
                $file_info = Db::name('File')->where(['sha1'=>$sha1])->find();

                if($file_info){
                    $return['data'][] = $file_info;
                    continue;
                }
            }
            
            //获取上传驱动
            $driver = modC('DOWNLOAD_UPLOAD_DRIVER','local','config');
            $driver = check_driver_is_exist($driver);
            
            if($driver == 'local'){
                $info = $file->validate(['size'=>$config['maxsize'],'ext'=>$config['mimetype']])->move($config['savepath']);
                if($info){
                    // 成功上传后 获取上传信息
                    $data['savepath'] = DS . 'uploads'  . DS . 'file'  . DS . $info->getSaveName();
                    $data['savepath'] = str_replace("\\","/",$data['savepath']);
                    $data['savename'] = str_replace("\\","/",$info->getSaveName());
                    $data['name'] = $info->getInfo()['name'];
                    $data['mime'] = $info->getMime();
                    $data['size'] = $info->getInfo()['size'];
                    $data['md5'] = $info->md5();
                    $data['sha1'] = $info->sha1();
                    $data['ext'] = substr(strrchr($data['savename'], '.'), 1);

                }else{
                    $this->error = $file->getError();
                    return false;
                }
            }else{
                //构建返回数据
                $data['driver'] = $driver;
                $data['name'] = $file->getInfo()['name'];
                $data['mime'] = $file->getInfo()['type'];
                $data['size'] = $file->getInfo()['size'];
                $data['md5'] = $file->hash('md5');
                $data['sha1'] = $file->hash('sha1');

                //调用驱动上传数据
                $res = $this->uploadDriver($driver, $file, $dirname);

                $data['savepath'] = $res['savepath'];
                $data['savename'] = $res['savename'];
                $data['ext'] = substr(strrchr($data['savename'], '.'), 1);
            }

            //写入数据库
            $data['create_time'] = time();
            $id = Db::name('file')->insertGetId($data);
            cache('file_path'.$id, NULL);
            cache('file_name'.$id, NULL);
            cache('file_all'.$id, NULL);
            if($id){
                $data['id'] = $id;
                $data['savepath'] = get_file_by_id($id);
                $return['data'][] = $data;
            }
        }
        return $return['data'];
    }

    /**
     * 图片上传
     *
     * @param      <type>         $files  The files
     *
     * @return     array|boolean  ( description_of_the_return_value )
     */
    private function Avatar($files, $dirname, $uid)
    {
        $config = config('upload.avatar');

        $return = [];
        foreach($files as $file){
            if (empty($files)) {
                $this->error = $file->getError();
                return false;
            }
            //判断是否已经存在暂不做处理 TODO
            

            //获取上传驱动
            $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
            $driver = check_driver_is_exist($driver);
            //构建返回数据
            
            if($driver == 'local'){
                $info = $file->validate(['size'=>$config['maxsize'],'ext'=>$config['mimetype']])->move($config['savepath'] . DS . $uid);
                
                if($info){
                    // 成功上传后 获取上传信息
                    $data['path'] = DS . 'uploads'  . DS . 'avatar' . DS . $uid . DS . $info->getSaveName();
                    $data['path'] = str_replace("\\","/",$data['path']);
                    //$data['md5'] = $info->md5();
                    //$data['sha1'] = $info->sha1();
                }else{
                    $this->error = $file->getError();
                    return false;
                }

            }else{
                //驱动上传
                //调用驱动上传数据
                $res = $this->uploadDriver($driver, $file, $dirname);

                if(isset($res['savepath'])){
                    $data['path'] = $res['savepath']; 
               }else{
                    $this->error = $res;
                    return false;
               }
            }

            //写入数据库
            $data['create_time'] = time();
            $data['driver'] = $driver;
            $data['status'] = 1;
            $have = Db::name('Avatar')->where(['uid'=>$uid])->find();

            if($have){
                $updateAvatar = Db::name('Avatar')->where(['uid'=>$uid])->update($data);
                if($updateAvatar){
                    $id = $have['id'];
                }

            }else{
                $data['uid'] = $uid;
                $id = Db::name('Avatar')->insertGetId($data);
            }

            if($id){
                $data['id'] = $id;
                $return[] = $data;  
            }
        }

        return $return;
    }

    /**
     * [base64 description]
     * @param  [type] $files [description]
     * @return [type]        [description]
     */
    public function base64($files)
    {

        $aData = $files;

        if ($aData == '' || $aData == 'undefined') {
            return false;
        }

        $result = [];
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $aData, $result)) {
            $base64_body = substr(strstr($aData, ','), 1);

            empty($aExt) && $aExt = $result[2];
        } else {
            $base64_body = $aData;
        }

        empty($aExt) && $aExt = 'jpg';

        $md5 = md5($base64_body);
        $sha1 = sha1($base64_body);

        $check = Db::name('Picture')->where(['md5' => $md5, 'sha1' => $sha1])->find();

        if ($check) {
            //已存在则直接返回信息
            $return['id'] = $check['id'];
            $return['path'] = $check['path'];

            return $return;

        } else {
            //不存在则上传并返回信息
            $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
            $driver = check_driver_is_exist($driver);
            $date = date('Y-m-d');
            $saveName = uniqid();
            $savePath = '/uploads/picture/' . $date . '/';

            $path = $savePath . $saveName . '.' . $aExt;
            if($driver == 'local'){
                //本地上传
                if(!file_exists('.' . $savePath)){
                    mkdir('.' . $savePath, 0777, true);
                }
                
                $data = base64_decode($base64_body);
                $rs = file_put_contents('.' . $path, $data);
            }
            else{
                $rs = false;
                //使用云存储
                $name = get_addon_class($driver);
                if (class_exists($name)) {
                    $class = new $name();
                    if (method_exists($class, 'uploadBase64')) {
                        $path = $class->uploadBase64($base64_body,$path);
                        $rs = true;
                    }
                }
            }
            if ($rs) {
                
                $pic['path'] = $path;
                $pic['driver'] = $driver;
                $pic['md5'] = $md5;
                $pic['sha1'] = $sha1;
                $pic['status'] = 1;
                $pic['create_time'] = time();
                $id = Db::name('picture')->insertGetId($pic);

                return ['id' => $id, 'path' => get_pic_src($path)];
            } else {
                return false;
            }
        }
    }

    /**
     * Uploads a driver.
     *
     * @param      <string>  $driver  驱动名称
     * @param      <type>    $file    文件
     * @param      <array>   $diyname  自定义目录
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function uploadDriver($driver, $file, $dirname)
    {
        //使用云存储
        $name = get_addon_class($driver);
        if (class_exists($name)) {
            $class = new $name();

            if (method_exists($class, $driver)) {
                $path = $class->$driver($file,$dirname);
                return $path;
            }
        }
    }

    
}
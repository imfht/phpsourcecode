<?php
namespace app\api\model;

use think\Model;
use think\Db;

class Upload extends Model
{

	public function upload($files,$type="picture")
	{

		if($type=='picture'){
			$result = $this->picture($files);
		}
		if($type=='file'){
			$result = $this->file($files);
		}
		if($type=='base64'){
			$result = $this->base64($files);
		}

		return $result;

	}

	private function picture($files)
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
                    $data['create_time'] = time();
                    $data['status'] = 1;
                    $data['driver'] = $driver;

                }else{
                    $this->error = $file->getError();
                    return false;
                }
            }else{
                //获取驱动配置
                $uploadConfig = get_upload_config($driver);
                //文件本地路径
                $filePath = $file->getRealPath();
            }

            //写入数据库
            $id = Db::name('Picture')->insertGetId($data);
            if($id){
                $data['id'] = $id;
                $data['path'] = pic($id);
                $return['data'][] = $data;  
            }
        }
        return $return['data'];
	}

	    /* 文件上传 */
    public function file($files)
    {   
        $config = config('upload.file');
        
        foreach($files as $file){
            if (empty($files)) {
	            $this->error = $file->getError();
	            return false;
	        }
            //判断是否已经存在附件
            $sha1 = $file->hash();
            //处理已存在图片
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
            //构建返回数据
            $data['driver'] = $driver;

            if($driver == 'local'){
                $info = $file->validate(['size'=>$config['maxsize'],'ext'=>$config['mimetype']])->move($config['savepath']);
                if($info){
                    // 成功上传后 获取上传信息
                    $data['name'] = $info->getInfo()['name'];
                    $data['mime'] = $info->getMime();
                    $data['size'] = $info->getInfo()['size'];
                    $data['savepath'] = DS . 'uploads'  . DS . 'file'  . DS . $info->getSaveName();
                    $data['savepath'] = str_replace("\\","/",$data['savepath']);
                    $data['savename'] = str_replace("\\","/",$info->getSaveName());
                    $data['ext'] = substr(strrchr($data['savename'], '.'), 1);
                    $data['md5'] = $info->md5();
                    $data['sha1'] = $info->sha1();
                    $data['create_time'] = time();

                }else{
                    $this->error = $file->getError();
                    return false;
                }
            }else{
                //获取驱动配置
                $uploadConfig = get_upload_config($driver);
                //文件本地路径
                $filePath = $file->getRealPath();
            }

            //写入数据库
            $id = Db::name('file')->insertGetId($data);
            if($id){
                $data['id'] = $id;
                $data['savepath'] = getFileById($id);
                $return['data'][] = $data;
            }
        }
        return $return['data'];
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
}
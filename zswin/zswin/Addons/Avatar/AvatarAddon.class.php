<?php

namespace Addons\Avatar;

use Common\Controller\Addon;

require_once('ThinkPHP/Library/Vendor/PHPImageWorkshop/ImageWorkshop.php');

use PHPImageWorkshop\Core\ImageWorkshopLayer;
use PHPImageWorkshop\ImageWorkshop;

/**
 * 头像插件插件
 * @author caipeichao
 */
class AvatarAddon extends Addon
{
    private $error;

    public $info = array(
        'name' => 'Avatar',
        'title' => '头像插件',
        'description' => '用于头像的上传',
        'status' => 1,
        'author' => 'zswin',
        'version' => '0.1'
    );

    public $admin_list = array(
        'model' => 'Avatar', //要查的表
        'fields' => '*', //要查的字段
        'map' => '', //查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
        'order' => 'id desc', //排序,
        'listKey' => array( //这里定义的是除了id序号外的表格里字段显示的表头名
            'uid' => 'UID',
            'path' => '保存路径',
            'create_time' => '上传时间',
        ),
    );

    public function install()
    {
        $prefix = C("DB_PREFIX");
        $model = D();
        $model->execute("DROP TABLE IF EXISTS {$prefix}avatar;");
        $model->execute("CREATE TABLE {$prefix}avatar (id int primary key auto_increment, uid int not null, path varchar(70) not null, create_time int not null, status int not null, is_temp int not null)");
        return true;
    }

    public function uninstall()
    {
        $prefix = C("DB_PREFIX");
        $model = D();
        $model->execute("DROP TABLE IF EXISTS {$prefix}avatar");
        return true;
    }

    public function avatar()
    {
    }
    

    public function getTempAvatar($uid)
    {
        //获取用户上传的临时头像
        $model = $this->getAvatarModel();
        $avatar = $model->getTempAvatar($uid);

        if ($avatar) {
            if (strtolower(APP_MODE) == 'sae') {
                return $avatar;
            }
            return getRootUrl()."Uploads/Avatar/$avatar";
        }

        return '';
    }
    private function saveUploadedFile($image)
    {
        $this->ensureAvatarFolderCreated();

        $config = $this->getUploadConfig();
        $model = D('Addons://Avatar/File');
        $upload = $model->upload(array('image' => $image), $config);

        if (!$upload) {
            $this->error = "写入磁盘失败";
            return false;
        }
        if (strtolower(APP_MODE) == 'sae') {
            return $path = $upload['image']['path']; //sae上最终取图方式

        } else {
          
        	
            return $upload['image']['savepath'] . $upload['image']['savename'];
        }

    }
    public function uploadTemp($uid, $image)
    {
        //检查参数
        if (!$uid) {
            $this->error = 'UID参数不能为空';
            return false;
        }
        if (!$image) {
            $this->error = '图像不能为空';
            return false;
        }

        //调用组件上传临时头像，得到图片路径
        $path = $this->saveUploadedFile($image);
        //保存临时头像
        $model = $this->getAvatarModel();
        $result = $model->saveTempAvatar($uid, $path);
        if (!$result) {
            $this->error = '写入数据库失败';
            return false;
        }
        //返回成功消息
       
        return true;
    }

    private function getAvatarModel()
    {
        return D('Addons://Avatar/Avatar');
    }
 
    private function getUploadConfig()
    {
        //TODO：将配置放在config.php中
        return array(
            'mimes' => '', //允许上传的文件MiMe类型
            'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
            'exts' => 'jpg,gif,png,jpeg', //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array('date', 'Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './Uploads/Avatar/', //保存根路径
            'savePath' => '', //保存路径
            'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt' => '', //文件保存后缀，空则使用原后缀
            'replace' => false, //存在同名是否覆盖
            'hash' => true, //是否生成hash编码
            'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
        );
    }
    private function getFullPath($path)
    {
        return "./Uploads/Avatar/$path";
    }
    
    
    
    
    
    
    /**
     * @param      $image
     * @param null $crop 数组，包含x,y,width,height
     * @return mixed
     */
    public function apply($uid, $crop)
    {
        //检查参数
        if (!$uid) {
            $this->error = 'uid参数不能为空';
            return false;
        }
        //读取现有头像
        $model = $this->getAvatarModel();
        $tempAvatar = $model->getTempAvatar($uid);
        if (!$tempAvatar) {
            $this->error = '找不到临时头像';
            return false;
        }
        //裁剪头像
        $path = $this->cropAvatar($tempAvatar, $crop);
        if (!$path) {
            $this->error = '裁剪头像失败：' . $this->error;
            return false;
        }
        //保存新头像
        $model->saveAvatar($uid, $path);
        //返回成功消息
        return true;
    }





    private function cropAvatar($path, $crop = null)
    {
        //如果不裁剪，则发生错误
        if (!$crop) {
            $this->error = '必须裁剪';
            return false;
        }


        //是sae则不需要获取全路径
        if (strtolower(APP_MODE) != 'sae') {
            //本地环境
            //获取头像的文件路径
            $fullPath = $this->getFullPath($path);
            //生成文件名后缀
            $postfix = substr(md5($crop), 0, 8);
            $savePath = preg_replace('/\.[a-zA-Z0-9]*$/', '-' . $postfix . '$0', $fullPath);
            $returnPath = preg_replace('/\.[a-zA-Z0-9]*$/', '-' . $postfix . '$0', $path);

            //解析crop参数
            $crop = explode(',', $crop);
            $x = $crop[0];
            $y = $crop[1];
            $width = $crop[2];
            $height = $crop[3];

            //载入临时头像
            $image = ImageWorkshop::initFromPath($fullPath);
           //如果宽度和高度近似相等，则令宽和高一样
            if (abs($height - $width) < $height * 0.01) {
                $height = min($height, $width);
                $width = $height;
            } else {
                $this->error = $height.'图像必须为正方形';
                return false;
            }

            //确认头像足够大
            if ($height < 256) {
                $this->error = '头像太小';
                return false;
            }


            //调用组件裁剪头像
            $image = ImageWorkshop::initFromPath($fullPath);
            $image->crop(ImageWorkshopLayer::UNIT_PIXEL, $width, $height, $x, $y);
            $image->save(dirname($savePath), basename($savePath));

            //返回新文件的路径
            return $returnPath;
        } else {

            //sae
            $fullPath = $path;
            $postfix = substr(md5($crop), 0, 8);
            $savePath = preg_replace('/\.[a-zA-Z0-9]*$/', '-' . $postfix . '$0', $fullPath);
            $returnPath = preg_replace('/\.[a-zA-Z0-9]*$/', '-' . $postfix . '$0', $path);

            //解析crop参数
            $crop = explode(',', $crop);
            $x = $crop[0];
            $y = $crop[1];
            $width = $crop[2];
            $height = $crop[3];


            //载入临时头像


            $f = new \SaeFetchurl();
            $img_data = $f->fetch($fullPath);
            $img = new \SaeImage();
            $img->setData($img_data);
            $img_attr = $img->getImageAttr();


            //生成将单位换算成为像素
            $x = $x * $img_attr[0];
            $y = $y * $img_attr[1];
            $width = $width * $img_attr[0];
            $height = $height * $img_attr[1];

            //如果宽度和高度近似相等，则令宽和高一样
            if (abs($height - $width) < $height * 0.01) {
                $height = min($height, $width);
                $width = $height;
            } else {
                $this->error = '图像必须为正方形';
                return false;
            }

            //确认头像足够大
            if ($height < 128) {
                $this->error = '头像太小';
                return false;
            }

     /*       dump('x='.$x);
            dump('y='.$y);
            dump('w='.$width);
            dump('h='.$height);exit;*/

            $img->crop($x / $img_attr[0], ($x+$width) / $img_attr[0], ($y) / $img_attr[1], ($y+$height) /  $img_attr[1]);
            $new_data = $img->exec();
            $storage = new \SaeStorage();
            $thumbFilePath = str_replace(C('UPLOAD_SAE_CONFIG.rootPath'), '', dirname($savePath) . '/' . basename($savePath));

            $thumbed = $storage->write(C('UPLOAD_SAE_CONFIG.domain'), $thumbFilePath, $new_data);

            //返回新文件的路径
            return $thumbed;


        }


    }

    public function getError()
    {
        return $this->error;
    }

    public function getAvatarUrl($uid)
    {
        $path = $this->getAvatarPath($uid);
        return getRootUrl() . $path;
    }

    public function getAvatarPath($uid,$avatarSize)
    {
        $model = D('Addons://Avatar/Avatar');
        $avatar = $model->getAvatar($uid);



        if ($avatar) {
            if (is_sae()) {
                $avatar_path=$avatar;

            }else{
                $avatar_path="/Uploads/Avatar/$avatar";
            }


           return getImageUrlByPath($avatar_path,$avatarSize);
        }else{
            //如果没有头像，返回默认头像
            if($avatarSize!=0){
                return getRootUrl()."Addons/Avatar/default_".$avatarSize."_".$avatarSize.".jpg";
            }else{
                return getRootUrl()."Addons/Avatar/default.jpg";
            }

        }

    }




    
    
    
   

    /**
     * 确认头像文件夹已经创建。
     *
     * 检查头像是否存在，如果不存在则创建文件夹。
     * @return void
     */
    private function ensureAvatarFolderCreated()
    {
        mkdir('./Uploads/Avatar');
    }
}
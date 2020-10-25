<?php
namespace Modules\Install\Library;

use Core\Config;
use Core\File;

class Common{
    public static function validationSave(\Modules\Form\Form $form){
        $data = $form->getData();
        $settings = Config::get('install.settings');
        if($data['password'] == $settings['password']){
            return $form;
        }else{
            return false;
        }
    }

    public static function oneSave(\Modules\Form\Form $form){
        global $di;
        $data = $form->getData();
        $_mysqli = new mysqli($data['dbHost'],$data['dbUser'],$data['dbPassword']);
        if (mysqli_connect_errno()) {
            $di->getShared('flash')->error('连接数据库出错');
            return false;
        }else{
            if(file_exists(ROOT_DIR.'Web/'.$data['siteName'])){
                $di->getShared('flash')->error('站点的机读名已经存在，安装终止');
            }
        }
    }
}
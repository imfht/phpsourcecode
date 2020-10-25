<?php

/**
 * 系统设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\system\admin;


class ConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '系统配置',
                'description' => '配置系统基本信息与设置',
            ),
        );
    }

    /**
     * 站点设置
     */
    public function index() {
        if(!isPost()) {
            $fieldList = target('SystemInfo')->loadList();
            $this->assign('fieldList', $fieldList);
            $this->systemDisplay();
        }else{
            if(target('SystemInfo')->saveInfo()){
                $this->success('站点配置成功！');
            }else{
                $this->error('站点配置失败');
            }
        }
    }

    /**
     * 性能设置
     */
    public function user(){
        $file = 'data/config/use/use';
        if(!isPost()){
            $config = load_config($file);
            $this->assign('info', $config['dux.use']);
            $this->assign('cacheList', \dux\Config::get('dux.cache'));
            $this->assign('storageList', \dux\Config::get('dux.storage'));
            $this->systemDisplay();
        }else{
            if(save_config($file, ['dux.use' => $_POST])){
                $this->success('性能配置成功！');
            }else{
                $this->error('性能配置失败');
            }
        }
    }

    /**
     * 系统信息
     */
    public function info(){
        $file = 'data/config/use/info';
        if(!isPost()){
            $config = load_config($file);
            $this->assign('info', $config['dux.use_info']);
            $this->systemDisplay();
        }else{
            if(save_config($file, ['dux.use_info' => $_POST])){
                $this->success('系统信息配置成功！');
            }else{
                $this->error('系统信息配置失败');
            }
        }
    }

    /**
     * 上传设置
     */
    public function upload(){
        $file = 'data/config/use/upload';
        if(!isPost()){
            $config = load_config($file);
            $this->assign('info', $config['dux.use_upload']);
            $this->assign('driverList', \dux\Config::get('dux.upload_driver'));
            $this->assign('imageList', \dux\Config::get('dux.image_driver'));
            $this->systemDisplay();
        }else{
            if(save_config($file, ['dux.use_upload' => $_POST])){
                $this->success('上传配置成功！');
            }else{
                $this->error('上传配置失败');
            }
        }
    }

}
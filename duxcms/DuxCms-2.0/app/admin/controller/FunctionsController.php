<?php
namespace app\admin\controller;
use app\admin\controller\AdminController;
/**
 * 应用管理
 */

class FunctionsController extends AdminController {

    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '应用管理',
                'description' => '管理网站基本功能',
                ),
            'menu' => array(
                    array(
                        'name' => '应用列表',
                        'url' => url('admin/Functions/index'),
                        'icon' => 'exclamation-circle',
                    ),
                )
            );
    }
	/**
     * 应用列表
     */
    public function index(){
        $breadCrumb = array('应用管理'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',target('Functions')->loadList());
        $this->adminDisplay();
    }

    /**
     * 状态更改
     */
    public function state(){
        $data = request('post.data');
        $app = request('get.app');
        //更新配置
        $config = array();
        $config['APP_STATE'] = $data;
        $file = $app.'/config';
        $info = load_config($file);
        if($info['APP_SYSTEM'] || !$info['APP_INSTALL']){
            $this->error('该应用无法更改状态！');
        }
        if(save_config($file, $config)){
            $this->success('应用状态更新成功！');
        }else{
            $this->error('应用状态更新失败！');
        }
    }

    /**
     * 安装
     */
    public function install(){
        $app = request('post.data');
        //读取配置
        $info = target('Functions')->getInfo($app);
        if(empty($info) || $info['APP_SYSTEM'] || $info['APP_INSTALL'] ){
            $this->error('该模块无法进行安装操作！');
        }
        //更改模块状态
        $config = array();
        $config['APP_INSTALL'] = 1;
        $config['APP_STATE'] = 1;
        $file = $app.'/config';
        if(!save_config($file, $config)){
            $this->error('应用配置失败，请确保应用写入权限！');
        }
        //执行SQL语句
        if($info['APP_INST_SQL']){
            $sql = $info['APP_INST_SQL'];
            if(!empty($info['APP_SQL_PRE'])){
                $sql = str_replace($info['APP_SQL_PRE'], config('DB_PREFIX'), $sql);
            }
            if(target('Functions')->execute($sql) === false){
                $this->error('应用安装数据导入失败！');
            }
        }
        //执行扩展安装方法
        if($info['APP_INST_MODEL']){
            if(!target($app.'/'.$info['APP_INST_MODEL'])->install()){
                $this->error(target($app.'/'.$info['APP_INST_MODEL'])->getError());
            }
        }
        $this->success('应用安装成功！');
    }

    /**
     * 卸载
     */
    public function uninstall(){
        $app = request('post.data');
        //读取配置
        $info = target('Functions')->getInfo($app);
        if(empty($info) || $info['APP_SYSTEM'] || !$info['APP_INSTALL'] ){
            $this->error('该模块无法进行卸载操作！');
        }
        //执行扩展卸载方法
        if($info['APP_INST_MODEL']){
            if(!target($app.'/'.$info['APP_INST_MODEL'])->uninstall()){
                $this->error(target($app.'/'.$info['APP_INST_MODEL'])->getError());
            }
        }
        //执行卸载SQL语句
        if($info['APP_UNINST_SQL']){
            $sql = $info['APP_UNINST_SQL'];
            if(!empty($info['APP_SQL_PRE'])){
                $sql = str_replace($info['APP_SQL_PRE'], config('DB_PREFIX'), $sql);
            }
            target('Functions')->execute($sql);
        }
        //更改模块状态
        $config = array();
        $config['APP_INSTALL'] = 0;
        $config['APP_STATE'] = 0;
        $file = APP_PATH. $app .'/Conf/config.php';
        if(!save_config($file, $config)){
            $this->error('应用配置失败，请确保应用写入权限！');
        }
        $this->success('应用卸载成功！');
    }
}


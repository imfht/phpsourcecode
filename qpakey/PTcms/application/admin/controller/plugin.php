<?php
class PluginController extends AdminController{

    /**
     * @var PluginModel
     */
    protected $model;

    public function init() {
        parent::init();
        $this->model=$this->model('plugin');
    }

    public function indexAction() {
        $this->view->list=$this->model->getlist();
    }

    public function installAction() {
        $key=$this->input->get('key','en','');
        $config=parse_ini_file(APP_PATH.'/common/plugin/'.$key.'/config.ini');
        $tags=$this->plugin->get();
        $hook=explode('|',$config['hook']);
        foreach($hook as $v){
            $tags[$v][]=$key;
            $tags[$v]=array_unique($tags[$v]);
        }
        $this->model('config')->where(array('key'=>'plugin'))->edit(array('value'=>json_encode($tags)));
        $this->success('安装成功');
    }

    public function uninstallAction() {
        $key=$this->input->get('key','en','');
        $config=parse_ini_file(APP_PATH.'/common/plugin/'.$key.'/config.ini');
        $tags=$this->plugin->get();
        $hook=explode('|',$config['hook']);
        foreach($hook as $v){
            if(!empty($tags[$v]) && in_array($key,$tags[$v])){
                unset($tags[$v][array_search($key,$tags[$v])]);
            }
        }
        $this->model('config')->where(array('key'=>'plugin'))->edit(array('value'=>json_encode($tags)));
        $this->success('卸载成功');
    }

    public function upgradeAction() {

    }

    public function configAction() {
        $key=$this->input->request('pluginkey','en','');
        $list=pt::import(APP_PATH.'/common/plugin/'.$key.'/config.php');
        if ($list){
            if ($this->request->ispost()){
                foreach($list as &$v){
                    if (isset($_POST[$v['key']])){
                        $v['value']=$_POST[$v['key']];
                    }
                }
                F(APP_PATH.'/common/plugin/'.$key.'/config.php',$list);
                $this->success('修改成功');
            }
            $this->view->pluginkey=$key;
            $this->view->list=$list;
        }else{
            $this->error('该插件无配置项');
        }
    }
}
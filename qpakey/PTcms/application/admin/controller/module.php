<?php
class moduleController extends AdminController{

    /**
     * @var ModuleModel
     */
    protected $model;

    public function init() {
        parent::init();
        $this->model=$this->model('module');
    }
    //模块列表
    public function indexAction() {
        $this->view->list=$this->model->getlist();
    }

    // 安装模块
    public function installAction() {
        $key=$this->input->get('key','en','');
        $configfile=APP_PATH.'/'.$key.'/config.ini';
        if (is_file($configfile)) {
            $config = parse_ini_file($configfile, true);
            if ($info=$this->model->where(array('key'=>$key))->find()){
                $this->error('模块已经安装过了');
            }
            //运行sql语句
            if (!empty($config['installsql'])){
                $sqlfile=APP_PATH.'/'.$key.'/'.$config['installsql'];
                if (!is_file($sqlfile)) $this->error('安装数据库文件不存在');
                $this->db()->execute(F($sqlfile));
            }
            //添加菜单
            if(isset($config['nodelist'])){
                $config['nodegroup']=isset($config['nodegroup'])?$config['nodegroup']:'';
                $this->model('adminnode')->install($config['nodelist'],$config['nodegroup'],$key);
            }
            //添加到模块表
            $data['key']=$key;
            $data['name']=$config['name'];
            $data['system']=0;
            $data['create_user_id']=$_SESSION['admin']['userid'];
            $data['create_time']=NOW_TIME;
            $data['status']=1;
            $this->model->add($data);
            $this->success('安装成功');
        }else{
            $this->error('模块配置文件不存在');
        }
    }

    // 卸载模块
    public function uninstallAction() {
        $key=$this->input->get('key','en','');
        $configfile=APP_PATH.'/'.$key.'/config.ini';
        if (is_file($configfile)) {
            $config = parse_ini_file($configfile, true);
            if (!$info=$this->model->where(array('key'=>$key))->find()){
                $this->error('模块尚未安装');
            }
            //运行sql语句
            if (isset($_GET['deldb']) && $_GET['deldb']==1 && !empty($config['uninstallsql'])){
                $sqlfile=APP_PATH.'/'.$key.'/'.$config['uninstallsql'];
                if (!is_file($sqlfile)) $this->error('卸载数据库文件不存在');
                $this->db()->execute(F($sqlfile));
            }
            //删除菜单
            if(isset($config['nodelist'])){
                $config['nodegroup']=isset($config['nodegroup'])?$config['nodegroup']:'';
                $this->model('adminnode')->uninstall($config['nodelist'],$config['nodegroup'],$key);
            }
            //从模块表删除
            $this->model->del(array('key'=>$key));
            $this->success('卸载成功');
        }else{
            $this->error('模块配置文件不存在');
        }
    }

    // 升级
    public function upgradeAction() {

    }

    public function configAction() {
        $key=$this->input->request('module','en','');
        $list=pt::import(APP_PATH.'/'.$key.'/set.php');
        if ($list){
            if ($this->request->ispost()){
                $config=array();
                foreach($list as &$v){
                    if (isset($_POST[$v['key']])){
                        $config[$v['key']]=$v['value']=$_POST[$v['key']];
                    }
                }
                F(APP_PATH.'/'.$key.'/set.php',$list);
                F(APP_PATH.'/'.$key.'/config.php',$config);
                $this->success('修改成功');
            }
            $this->view->module=$key;
            $this->view->list=$list;
        }else{
            $this->error('该插件无配置项');
        }
    }
}
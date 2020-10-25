<?php
class ModuleModel extends PT_Model{

    /**
     * 插入数据
     * @param $param
     * @return mixed
     */
    public function add($param) {
        $res=$this->insert($param);
        $this->updateconfig();
        return $res;
    }

    /**
     * 修改
     * @param $param
     * @return mixed
     */
    public function edit($param) {
        //更新缓存
        $res=$this->update($param);
        $this->updateconfig();
        return $res;
    }

    /**
     * 删除数据
     * @param $where
     */
    public function del($where) {
        $this->where($where)->delete();
        $this->updateconfig();
    }

    public function getlist() {
        $fp = opendir(APP_PATH);
        $list = array();
        $infolist=$this->getfield('key,name,id,system,status,create_user_id,create_time',true);
        while ($path = readdir($fp)) {
            $file = APP_PATH . '/' . $path;
            if ($path != '.' && $path != '..' && $path != 'common' && $path != 'admin' && is_dir($file)) {
                $configfile = $file . '/config.ini';
                if (is_file($configfile)) {
                    $config = parse_ini_file($configfile, true);
                    $list[$path] = $config;
                    if (isset($infolist[$path])){
                        //已安装
                        $list[$path]['issetup']=($infolist[$path]['system']==1)?2:1;
                        $list[$path]['status']=$infolist[$path]['status'];
                        $list[$path]['id']=$infolist[$path]['id'];
                        $list[$path]['create_username']=$this->get('user',$infolist[$path]['create_user_id'],'name');
                        $list[$path]['create_time']=$infolist[$path]['create_time']?date('Y-m-d H:i',$infolist[$path]['create_time']):'';
                    }else{
                        //未安装
                        $list[$path]['issetup']=0;
                        $list[$path]['status']=-1;
                        $list[$path]['id']=0;
                        $list[$path]['create_username']='';
                        $list[$path]['create_time']='';
                    }
                    $list[$path]['key']=$path;
                    $list[$path]['url_install']=U('admin.module.install',array('key'=>$path));
                    $list[$path]['url_uninstall']=U('admin.module.uninstall',array('key'=>$path));
                    $list[$path]['url_config']=U('admin.module.config',array('module'=>$path));
                    unset($list[$path]['nodelist'],$list[$path]['installsql'],$list[$path]['unstallsql'],$list[$path]['nodegroup']);
                }
            }
        }
        return $list;
    }

    public function updateconfig() {
        $data=array();
        // 添加到可用模块列表配置
        $modules=$this->where(array('status'=>1))->getField('key',true);
        $this->model('config')->where(array('key'=>'allow_module'))->edit(array('value'=>implode(',',$modules)));
        // 添加的设置默认模块配置
        $list=$this->where(array('status'=>1))->field('key,name')->select();
        foreach($list as $v){
            $data[$v['key']]=$v['name'];
        }
        $this->model('config')->where(array('key'=>'default_module'))->edit(array('extra'=>$this->response->jsonEncode($data)));
    }
}
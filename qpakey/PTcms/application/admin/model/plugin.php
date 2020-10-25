<?php
class PluginModel extends PT_Model{
    public function getlist() {
        $pluginlist=$this->plugin->getlist();
        $fp = opendir(APP_PATH.'/common/plugin');
        $list = array();
        while ($path = readdir($fp)) {
            $file = APP_PATH.'/common/plugin' . '/' . $path;
            if ($path != '.' && $path != '..' && $path != 'common' && is_dir($file)) {
                $configfile = $file . '/config.ini';
                if (is_file($configfile)) {
                    $config = parse_ini_file($configfile, true);
                    $list[$path] = $config;
                    if (in_array($path,$pluginlist)){
                        //已安装
                        $list[$path]['setup']=1;
                    }else{
                        //未安装
                        $list[$path]['setup']=0;
                    }
                    $list[$path]['key']=$path;
                    $list[$path]['url_install']=U('admin.plugin.install',array('key'=>$path));
                    $list[$path]['url_uninstall']=U('admin.plugin.uninstall',array('key'=>$path));
                    $list[$path]['url_config']=U('admin.plugin.config',array('pluginkey'=>$path));
                }
            }
        }
        return $list;
    }
}
<?php

class themeModel extends PT_Model{

    public function getlist()
    {
        $fp=opendir(TPL_PATH);
        $list=array();
        while($path=readdir($fp)){
            $config=$this->getinfo($path);
            if ($config) $list[$path]=$config;
        }
        return $list;
    }

    public function getinfo($path) {
        $config=array();
        $file=TPL_PATH.'/'.$path;
        if ($path!='.' && $path!='..' && is_dir($file)){
            $configfile=$file.'/config.ini';
            if (is_file($configfile)){
                $config=parse_ini_file($configfile,true);
                $config['demo']=is_file($file.'/demo.jpg')?str_replace(PT_ROOT,'',$file.'/demo.jpg'):PT_DIR.'/public/image/nopic.jpg';
                switch($config['type']){
                    case 'wap':
                        $config['typename']='手机模版';
                        break;
                    case 'pc':
                        $config['typename']='PC模版';
                        break;
                    case 'all':
                        $config['typename']='响应式';
                        break;
                    default:
                        $config['typename']='未设置';
                }
            }
        }
        return $config;
    }
}
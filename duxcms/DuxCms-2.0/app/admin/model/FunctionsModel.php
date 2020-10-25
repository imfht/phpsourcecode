<?php
namespace app\admin\model;
/**
 * 应用操作
 */
class FunctionsModel {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList(){
        $list = glob(APP_PATH.'*/conf/config.php');
        $configArray = array();
        foreach ($list as $file) {
            //解析模块名
            $file = str_replace('\\', '/', $file);
            $fileName = explode('/', $file);
            $fileName = array_slice($fileName,-3,1);
            $fileName = $fileName[0];
            $configArray[$fileName] = $this->getInfo($fileName);
        }
        $configArray = array_order($configArray,'APP_SYSTEM');
        return $configArray;

    }

    /**
     * 添加APP信息
     * @param string $app 应用名
     */
    public function getInfo($app){
        $info = load_config($app.'/config');
        if(empty($info)){
            return ;
        }
        $info['APP'] = $app;
        $info['APP_DIR'] =  $app;
        if($info['APP_SYSTEM']){
            $info['APP_STATE'] = 1;
            $info['APP_INSTALL'] = 1;
        }
        return $info;
    }

}

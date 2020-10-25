<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 安全统计
 */
class SafeModel {

    /**
     * 获取安全测试结果
     */
    public function getList()
    {
        $safeArray = array();
        //数据库弱口令
        if(preg_match('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*\W).+$/', config('DB.default.DB_PWD'))){
            $safeArray['db'] = true;
        }
        //登录密码检测
        $list = target('admin/AdminUser')->loadList();
        $safeArray['user'] = true;
        foreach ($list as $value) {
            if($value['password'] == '21232f297a57a5a743894a0e4a801fc3'){
                $safeArray['user'] = false;
            }
        }
        //后台入口检测
        if(!is_file(__ROOT__ . '/admin.php')){
            $safeArray['login'] = true;
        }
        //上传设置检测
        $ext = 'php,asp,jsp,aspx';
        $extArray = explode(',', $ext);
        $safeArray['upload'] = true;
        foreach ($extArray as $value) {
            if(strstr($value, config('upload_exts'))){
                $safeArray['upload'] = false;
            }
        }
        //安装模块检测
        if(!is_dir(APP_PATH . 'install')){
            $safeArray['install'] = true;
        }
        return $safeArray;
    }

    /**
     * 获取安全评分
     */
    public function getCount()
    {
        $list = $this->getList();
        $count = 0;
        foreach ($list as $value) {
            if($value){
                $count = $count + 20;
            }
        }
        return $count;
    }

}

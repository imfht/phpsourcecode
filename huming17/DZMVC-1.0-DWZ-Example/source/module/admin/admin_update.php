<?php

/**
 * 升级管理
 * @author HumingXu E-mail:huming17@126.com
 */
switch ($do) {
     case "index":
     case "update":
         $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
         if($is_submit){
            $update_package = isset($_POST['update_package']) ? $_POST['update_package'] : '';
            $update_package_path = SITE_ROOT.'./'.$update_package;
            //DEBUG 执行操作更新
            if(file_exists($update_package_path)){
                //DEBUG 校验更新包
                $check_pku_return = check_pku($update_package_path);
                //DEBUG 执行更新文件或数据库写入新版本到版本库文件
                if(!empty($check_pku_return['patch']['isupdatedb'])){
                    //DEBUG sql 简单升级方法待优化
                    $sql_file = DZF_ROOT.'./data/offline_update/sql_data/';
                    if(file_exists($sql_file.'install.sql')){
                        $sql=file_get_contents($sql_file.'install.sql');
                        if($sql){
                            $sql=str_replace('@tableprefix@',$_G['config']['db']['1']['tablepre'],$sql);
                            runquery($sql);   
                        }
                    }
                    if(file_exists($sql_file.'install_data.sql')){
                        $sql=file_get_contents($sql_file.'install_data.sql');
                        if($sql){
                            $sql=str_replace('@tableprefix@',$_G['config']['db']['1']['tablepre'],$sql);
                            runquery($sql);   
                        }
                    }
                }
                //DEBUG 执行文件更新
                $offline_upgrade = new upgrade();
                $offline_upgrade->copy_dir($check_pku_return['data_cache_pku'].'/upload/', realpath(SITE_ROOT).'/');
                //DEBUG 更新后数据库插件版本标识设置
                $cachedata  = 'if(!defined("SITE_VERSION")) {
                    define("SITE_VERSION", "'.$check_pku_return['patch']['latestversion'].'");
                    define("SITE_RELEASE", "'.$check_pku_return['patch']['latestrelease'].'");
                    define("SITE_FIXBUG", "'.$check_pku_return['patch']['site_fixbug'].'");
                }
                ';
                writetocache('system_version', $cachedata, $prefix = '', SITE_ROOT.'./source/');
                //DEBUG 删除源更新包
                //$offline_upgrade->rmdirs($check_pku_return['data_cache_pku']);
                $return_message = lang('core','operation_successful');
            }else{
                $return_message = lang('core','file_not_exist');
            }
            echo '{
                "statusCode":"200",
                "message":"'.$return_message.'",
                "navTabId":"",
                "rel":"",
                "callbackType":"forward",
                "forwardUrl":"admin.php?mod=update&action=index",
                "confirmMsg":""
            }';
            die();
         }
         include template('admin/update/edit');
         break;
     
    default:
        //DEBUG 查询并返回信息链接

        include template('admin/update/list');
        break;
}
?>
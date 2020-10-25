<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-2-28
 * Time: 下午4:29
 * @author 郑钟良<zzl@ourstu.com>
 */
require_once(ONETHINK_ADDON_PATH.'Skin/Config/config.php');
/**
 * 获取可用皮肤列表
 * @author 郑钟良<zzl@ourstu.com>
 */
function getSkinList()
{
    $dirname = SKIN_PATH.'Skins';
    $skinList=array();
    if (is_dir($dirname)) {
        if ($skins = opendir($dirname)) {
            while (($file = readdir($skins)) !== false) {
                if($file!=='.'&&$file!=='..'){
                    $skinList[]['file_name'] = $file;
                }
            }
            closedir($skins);
        }
    }
    $skinList=array_column($skinList,'file_name');

    $skinInfoList=getSkinInfoList($skinList);
    return $skinInfoList;
}

/**
 * 根据皮肤列表，获取皮肤详细信息
 * @param $skinList 皮肤列表
 * @return array
 * @author 郑钟良<zzl@ourstu.com>
 */
function getSkinInfoList($skinList){
    $skinInfoList=array();
    foreach($skinList as $path) {
        $skinConf =include(SKIN_PATH.'Skins/'.$path.'/config.php');
        $skin['value']=$path;
        $skin['name'] = $skinConf['name'];
        $skin['sort'] = $skinConf['sort'];
        $skin['thumb_url'] = SKIN_PATH.'Skins/'.$path.'/thumb.png';
        $skinInfoList[] = $skin;
    }
    unset($path,$skin);
    $skinInfoList=list_sort_by($skinInfoList,'sort','asc');
    return $skinInfoList;
}

/**
 * 获取插件配置信息
 * @return mixed
 * @author 郑钟良<zzl@ourstu.com>
 */
function getAddonConfig(){
    $config=S('SKIN_ADDON_CONFIG');
    if(!$config){
        $map['name']    =   ADDON_NAME;
        $map['status']  =   1;
        $config  =   M('Addons')->where($map)->getField('config');
        $config=json_decode($config,true);
        S('SKIN_ADDON_CONFIG',$config,600);
    }
    return $config;
}

/**
 * 获取用户插件信息
 * @return mixed
 * @author 郑钟良<zzl@ourstu.com>
 */
function getUserConfig()
{
    $UserConfig=S('SKIN_USER_CONFIG_'.is_login());
    if(!$UserConfig){
        $map=getUserConfigMap(USER_CONFIG_MARK_NAME,USER_CONFIG_MARK_MODEL,get_login_role());
        $skin  =   M('UserConfig')->where($map)->getField('value');
        if(!$skin){
            $UserConfig=getAddonConfig();
            $UserConfig['skin']=$UserConfig['defaultSkin'];
        }else{
            $UserConfig['skin']=$skin;
        }
        S('SKIN_USER_CONFIG_'.is_login(),$UserConfig,600);
    }
    return $UserConfig;
}
<?php

function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '') {
    echo json_encode(array('statusCode'=>300,'message'=>$msg));
	exit;
}

/**
 * 模板风格列表
 * @param integer $siteid 站点ID，获取单个站点可使用的模板风格列表
 * @param integer $disable 是否显示停用的{1:是,0:否}
 */
function template_list($siteid = '', $disable = 0) {
    $list = glob(APP_PATH.'Home'.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
    $arr = $template = array();
    // if ($siteid) {
    //     $site = pc_base::load_app_class('sites','admin');
    //     $info = $site->get_by_id($siteid);
    //     if($info['template']) $template = explode(',', $info['template']);
    // }
    foreach ($list as $key=>$v) {
        $dirname = basename($v);
        // if ($siteid && !in_array($dirname, $template)) continue;
        if (file_exists($v.DIRECTORY_SEPARATOR.'config.php')) {
            $arr[$key] = include $v.DIRECTORY_SEPARATOR.'config.php';
            if (!$disable && isset($arr[$key]['disable']) && $arr[$key]['disable'] == 1) {
                unset($arr[$key]);
                continue;
            }
        } else {
            $arr[$key]['name'] = $dirname;
        }
        $arr[$key]['dirname']=$dirname;
    }
    return $arr;
}

function unzip($zipName, $extract_path){
    $zip = new \ZipArchive;
    //中文文件名要使用ANSI编码的文件格式
    if ($zip->open($zipName) === true) {
        $zip->extractTo($extract_path); //提取全部文件
        //$zip->extractTo('/my/destination/dir/', array('pear_item.gif', 'testfromfile.php'));//提取部分文件
        $zip->close();
        return true;
    }

    return false;
}

function zip($dir_path,$zipName){
    $relationArr = [$dir_path=>[
        'originName'=>$dir_path,
        'is_dir' => true,
        'children'=>[]
    ]];
    modifiyFileName($dir_path,$relationArr[$dir_path]['children']);
    $zip = new \ZipArchive();
    $zip->open($zipName,ZipArchive::CREATE);
    zipDir(array_keys($relationArr)[0],'',$zip,array_values($relationArr)[0]['children']);
    $zip->close();
    restoreFileName(array_keys($relationArr)[0],array_values($relationArr)[0]['children']);
}

function zipDir($real_path,$zip_path,&$zip,$relationArr){
    $sub_zip_path = empty($zip_path)?'':$zip_path.DIRECTORY_SEPARATOR;
    if (is_dir($real_path)){
        foreach($relationArr as $k=>$v){
            if($v['is_dir']){  //是文件夹
                $zip->addEmptyDir($sub_zip_path.$v['originName']);
                zipDir($real_path.DIRECTORY_SEPARATOR.$k,$sub_zip_path.$v['originName'],$zip,$v['children']);
            }else{ //不是文件夹
                $zip->addFile($real_path.DIRECTORY_SEPARATOR.$k,$sub_zip_path.$k);
                $zip->deleteName($sub_zip_path.$v['originName']);
                $zip->renameName($sub_zip_path.$k,$sub_zip_path.$v['originName']);
            }
        }
    }
}
function modifiyFileName($path,&$relationArr){
    if(!is_dir($path) || !is_array($relationArr)){
        return false;
    }
    if($dh = opendir($path)){
        $count = 0;
        while (($file = readdir($dh)) !== false){
            if(in_array($file,['.','..',null])) continue; //无效文件，重来
            //mac无效文件
            if(strpos($file, 'DS_Store') !== false){
                continue;
            }
            if(is_dir($path.DIRECTORY_SEPARATOR.$file)){
                $newName = md5(rand(0,99999).rand(0,99999).rand(0,99999).microtime().'dir'.$count);
                $relationArr[$newName] = [
                    'originName' => iconv('GBK','UTF-8',$file),
                    'is_dir' => true,
                    'children' => []
                ];
                rename($path.DIRECTORY_SEPARATOR.$file, $path.DIRECTORY_SEPARATOR.$newName);
                modifiyFileName($path.DIRECTORY_SEPARATOR.$newName,$relationArr[$newName]['children']);
                $count++;
            }
            else{
                $extension = strchr($file,'.');
                $newName = md5(rand(0,99999).rand(0,99999).rand(0,99999).microtime().'file'.$count);
                $relationArr[$newName.$extension] = [
                    'originName' => iconv('GBK','UTF-8',$file),
                    'is_dir' => false,
                    'children' => []
                ];
                rename($path.DIRECTORY_SEPARATOR.$file, $path.DIRECTORY_SEPARATOR.$newName.$extension);
                $count++;
            }
        }
    }
}
function restoreFileName($path,$relationArr){
    foreach($relationArr as $k=>$v){
        if(!empty($v['children'])){
            restoreFileName($path.DIRECTORY_SEPARATOR.$k,$v['children']);
            rename($path.DIRECTORY_SEPARATOR.$k,iconv('UTF-8','GBK',$path.DIRECTORY_SEPARATOR.$v['originName']));
        }else{
            rename($path.DIRECTORY_SEPARATOR.$k,iconv('UTF-8','GBK',$path.DIRECTORY_SEPARATOR.$v['originName']));
        }
    }
}

// 循环创建目录 
function mk_dir($dir, $mode = 0755){ 
    if (is_dir($dir) || mkdir($dir,$mode)) return true; 
    if (!mk_dir(dirname($dir),$mode)) return false; 
    return mkdir($dir,$mode); 
} 

function deleteDir($dir)
{
    if (!$handle = @opendir($dir)) {
        return false;
    }
    while (false !== ($file = readdir($handle))) {
        if ($file !== "." && $file !== "..") {       //排除当前目录与父级目录
            $file = $dir . '/' . $file;
            if (is_dir($file)) {
                deleteDir($file);
            } else {
                @unlink($file);
            }
        }
    }
    @rmdir($dir);
}

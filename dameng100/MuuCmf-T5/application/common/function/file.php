<?php
use think\Db;
/**通过ID获取到文件
 * @param $file_id 文件的ID
 * @param int $type 文件的类型，qiniu 七牛，local 本地, sae SAE
 * @param bool $replace 是否强制替换
 * @return string
 */
function get_file_by_id($file_id, $type = 0, $replace = false)
{

    $file = cache('file_path' . $file_id);
    if (empty($file)) {
        if($file_id>0){
            $file = Db::name('File')->find($file_id);
            cache('file_path' . $file_id, $file);
        }else{
            return "文件id不存在！";
        }
    }

    if ($file) {
        $path = $file['savepath'];
        return $path;
    } else {
        return "文件id不存在！";
    }
}
/**
 * 通过ID获取文件原始名
 * @param  [type]  $file_id [description]
 * @param  integer $type    [description]
 * @param  boolean $replace [description]
 * @return [type]           [description]
 */
function get_filename_by_id($file_id)
{
    $file = cache('file_name' . $file_id);
    if (empty($file)) {
        $file = Db::name('File')->find($file_id);
        cache('file_name' . $file_id, $file);
    }

    if ($file) {
        $name = $file['name'];
        return $name;
    } else {
        return "文件id不存在！";
    }
}

/**
 * 获取文件所有数据
 *
 * @param      <type>  $file_id  The file identifier
 *
 * @return     string  The fileall by identifier.
 */
function get_fileall_by_id($file_id)
{
    $file = cache('file_all' . $file_id);
    if (empty($file)) {
        $file = Db::name('File')->field('id,name,savepath,savename,ext,mime,size,driver')->find($file_id);
        cache('file_all' . $file_id, $file);
    }

    if ($file) {
        return $file;
    } else {
        return "文件不存在！";
    }
}


/**
 * get_pic_src   渲染文件链接
 * @param $path
 * @return mixed
 */
function get_file_src($path)
{
    //不存在http://
    $not_http_remote=(strpos($path, 'http://') === false);
    //不存在https://
    $not_https_remote=(strpos($path, 'https://') === false);
    if ($not_http_remote && $not_https_remote) {
        //本地url
        return str_replace('//', '/', $path); //防止双斜杠的出现
    } else {
        //远端url
        return $path;
    }
}
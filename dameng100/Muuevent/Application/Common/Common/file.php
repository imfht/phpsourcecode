<?php
/**通过ID获取到文件
 * @param $file_id 文件的ID
 * @param int $type 文件的类型，qiniu 七牛，local 本地, sae SAE
 * @param bool $replace 是否强制替换
 * @return string
 */
function getFileById($file_id, $type = 0, $replace = false)
{

    $file = S('file_' . $file_id);
    if (empty($file)) {
        $file = M('File')->where(array('status' => 1))->find($download_id);
        S('file_' . $file_id, $file);
    }

    if ($file) {
        $path = $file['savepath'].$file['savename'];
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
function getFileNameById($file_id)
{
    $file = S('file_' . $file_id);
    if (empty($file)) {
        $file = M('File')->find($file_id);
        S('file_' . $file_id, $file);
    }

    if ($file) {
        $name = $file['name'];
        return $name;
    } else {
        return "文件id不存在！";
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
        return str_replace('//', '/', getRootUrl() . $path); //防止双斜杠的出现
    } else {
        //远端url
        return $path;
    }
}
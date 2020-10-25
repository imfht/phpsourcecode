<?php
namespace app\admin\model;
/**
 * 网站管理
 */
class ManageModel {
    /**
     * 获取缓存列表
     * @param int $key 缓存key
     * @return array 用户信息
     */
    public function getCacheList($key = '')
    {
        $list = array(
                'tpl' => array('id'=>'tpl','name'=>'模板缓存', 'dir'=>DATA_PATH.'cache', 'size'=>(dir_size(DATA_PATH.'cache')%1024).'KB'),
                );
        if($key){
            return $list[$key];
        }
        return $list;
    }
    /**
     * 清空指定缓存
     * @param int $key 缓存key
     * @return array 用户信息
     */
    public function delCache($key)
    {
        $info = $this->getCacheList($key);
        if(empty($info)){
            return;
        }
        $file = $info['dir'];
        if(is_dir($file)){
            del_dir($file);
        }elseif(is_file($file)){
            unlink($file);
        }
        return true;
    }


}

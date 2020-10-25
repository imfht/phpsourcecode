<?php

class globalModel extends Model
{

    /**
     * 获取配置信息写入缓存
     */
    public function global_cache() {
        $global = array();
        $res = $this->getField('name,data');
        
        foreach ($res as $key=>$val) {
            $global['wkcms_'.$key] = unserialize($val) ? unserialize($val) : $val;
        }
        F('global', $global);
        return $global;
    }

    /**
     * 后台有更新则删除缓存
     */
    protected function _before_write($data, $options) {
        F('global', NULL);
    }
}
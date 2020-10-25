<?php
namespace Common\Model;
use Think\Model;
/**
 * 生成多层树状下拉选框的工具模型
 */
class SettingModel extends Model {
    /**
     * 获取配置信息写入缓存
     */
    public function setting_cache() {
        $setting = array();
        $res = $this->getField('name,data');
        foreach ($res as $key=>$val) {
            $setting['ik_'.$key] = unserialize($val) ? unserialize($val) : $val;
        }
        F('setting', $setting);
        return $setting;
    }

}


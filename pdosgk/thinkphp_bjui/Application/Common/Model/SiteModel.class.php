<?php 
/*
 * 网站设置
 */
namespace Common\Model;
use Think\Model;
class SiteModel extends Model {
    public function update($info){
        $this->where(['id' => 1])->save($info);
        return true;
    }

    public function getSetting($cache_enable = false){
        $detail = $this->where(['id' => 1])->find();
        $detail['setting'] = string2array($detail['setting']);
        return $detail;
    }
}

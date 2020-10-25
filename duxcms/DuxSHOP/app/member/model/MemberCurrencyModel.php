<?php

/**
 * 货币管理
 */
namespace app\member\model;

class MemberCurrencyModel {

    /**
     * 获取货币接口
     * @return array
     */
    public function typeList() {
        $list = hook('service', 'Type', 'Currency');
        $data = [];
        foreach ($list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        return $data;
    }

}
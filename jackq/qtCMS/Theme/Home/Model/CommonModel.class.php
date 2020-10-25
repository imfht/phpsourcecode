<?php

namespace Home\Model;

use Think\Model\RelationModel;

/**
 * CommonModel
 * 数据库、数据表信息操作
 */
class CommonModel extends RelationModel {

    /**
     * 数据表是否有记录
     * @param  string  $tableName
     * @return boolean
     */
    public function hasRecord($tableName) {
        $result = $this->query("SELECT COUNT(*) FROM {$tableName}");
        if ($result[0]['COUNT(*)']) {
            return true;
        }
        return false;
    }


}

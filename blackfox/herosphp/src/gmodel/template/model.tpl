<?php
/**
 * {desc}
 * @author  {author} <{email}>
 */

namespace app\{module}\dao;

use herosphp\model\MysqlModel;

class {model_name} extends MysqlModel {

    public function __construct() {

        //创建model对象并初始化数据表名称
        parent::__construct('{table_name}');

        //设置表数据表主键，默认为id
        $this->primaryKey = 'id';
    }
} 
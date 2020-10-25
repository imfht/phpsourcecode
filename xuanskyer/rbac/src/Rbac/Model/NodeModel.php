<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: xuanskyer | <furthestworld@iloucd.com>
 * Date: 2016/12/13 9:53
 */

namespace Rbac\Model;

class NodeModel extends Model {
    public function __construct($setting = []){
        $this->db_conf_name = $setting['dbname'];
        $this->table_name = $setting['table_name'];
        parent::__construct($setting);
    }
}
<?php
namespace Modules\File\Models;

use Phalcon\Mvc\Model;

class File_log extends Model {

    //重新定义数据库表
    static public $tableName;
    public function getSource() {
        return self::$tableName;
    }

}
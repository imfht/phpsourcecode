<?php
namespace backend\services;

use backend\models\AdminRight;

class AdminRightService extends AdminRight{

    public function getAllRight(){
        $sql = "SELECT m.id AS mid, m.display_label AS m_name, f.id AS fid, f.code, f.menu_name AS f_name, r.id AS rid, r.right_name AS r_name FROM
        admin_module m LEFT JOIN admin_menu f ON f.module_id = m.id
        LEFT JOIN admin_right r ON r.menu_id = f.id";
        //$connection = Yii::$app->db;
        $connection = $this->getDb();
        $command=$connection->createCommand($sql);
        $rows=$command->queryAll();
        //         $rows=$dataReader->readAll();
        return $rows;
    }
}

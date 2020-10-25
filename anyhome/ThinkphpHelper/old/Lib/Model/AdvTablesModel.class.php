<?php

class AdvTablesModel extends Model
{
    function getTables(){
        $tbs =  $this->db->getTables();
        $volist = array();
        foreach ($tbs as $k) {
            $vo = array();
            $vo['trueTableName'] = $k;
            $vo['tableName'] = str_replace(C('DB_PREFIX'), "", $k);
            $volist[] = $vo;
        }
        return $volist;
    }
}
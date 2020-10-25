<?php
namespace think\db\connector;

use PDO;
use think\db\Connection;
/**
 * Redshift数据库驱动
 * by Joffe
 */
class Redshift extends Pgsql
{
    /**
     * 取得数据表的字段信息
     * @access public
     * @return array
     */
    public function getFields($tableName)
    {
        list($tableName) = explode(' ', $tableName);//屏蔽as等后面内容
        $tmp = explode('.',$tableName,2);
        if(count($tmp) == 2){
             $schemaname = $tmp[0];
             $table = $tmp[1];
        }else{
            $schemaname='public';
            $table = $tmp[0];
        }
        $result          = $this->query("select \"column\",\"type\",\"notnull\" from pg_table_def where schemaname = '$schemaname' and tablename = '$table';");
        $info            = [];
        if ($result) {
            foreach ($result as $val) {
                $info[$val['column']] = [
                    'name'    => $val['column'],
                    'type'    => $val['type'],
                    'notnull' => $val['notnull'],
                    //'default' => $val['default'],
                    'primary' => '',
                    'autoinc' => '',
                ];
            }
        }
        //dump($info);
        return $info;
    }

}
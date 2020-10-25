<?php
/**
 * iPHP - i PHP Framework
 * Copyright (c) iiiPHP.com. All rights reserved.
 *
 * @author iPHPDev <master@iiiphp.com>
 * @website http://www.iiiphp.com
 * @license http://www.iiiphp.com/license
 * @version 2.1.0
 */

class iDB extends iDataBase {
    public static function link() {
        extension_loaded('PDO') OR self::print_error('PDO extension is missing. Please check your PHP configuration');
        extension_loaded('PDO_SQLITE') OR self::print_error('PDO_SQLITE extension is missing. Please check your PHP configuration');

        try {
            // self::$link = new PDO('sqlite:'.iPHP_APP_CORE.'/#'.md5(iPHP_KEY).'.db.php', null, null, array(PDO::ATTR_PERSISTENT => true));
            self::$link = new PDO('sqlite:'.iPHP_APP_CORE.'/iCMS7.db', null, null, array(PDO::ATTR_PERSISTENT => true));
        } catch (PDOException $e) {
            self::print_error('Connection failed:'.$e->getMessage());
        }
    }
    public static function ping() {
        return mysql_ping(self::$link);
    }
    public static function pre_set() {
        self::$link->query('PRAGMA encoding = "'.self::$config['CHARSET'].'"');
        self::$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public static function select_db($flag=false) {
        // $sel = @mysql_select_db(self::$config['DB'], self::$link);
        // if($flag) return $sel;
        // $sel OR self::print_error('Select Db Error');
    }
    // ==================================================================
    /** Quote string to use in SQL
    * @param string
    * @return string escaped string enclosed in '
    */
    public static function quote($string) {
        return "'" . self::$link->quote($string) . "'";
    }

    //  Basic Query - see docs for more detail
    public static function query($query,$QT=NULL) {
        $query = self::pre_query($query);
        if($query===false) return false;

        $return_val = 0;
        try{
            self::$result = self::$link->query($query,PDO::FETCH_OBJ);
        }catch(PDOException  $e ){
            echo $e->getMessage();
            // var_dump($e);
            return false;
        }

        if(self::$result===false){
            // If there is an error then take note of it..
            return self::print_error();
        }

        if(strpos($query,'EXPLAIN')===false){
            self::$num_queries++;
            self::$show_trace && self::backtrace($query);
        }

        self::$show_trace && self::timer_start();

	    if($QT=='get') return self::$result;

        $QH = strtoupper(substr($query,0,strpos($query, ' ')));
        if (in_array($QH,array('INSERT','DELETE','UPDATE','REPLACE','SET','CREATE','DROP','ALTER'))) {
            $rows_affected = self::$result->rowCount();
            // Take note of the insert_id
            if (in_array($QH,array("INSERT","REPLACE"))) {
                self::$insert_id = self::$link->lastInsertId();
            }
            // Return number of rows affected
            $return_val = $rows_affected;
        } else {
            if($QT=="field") {
                self::$col_info[] = self::$result->getColumnMeta();
            }else {
                $QH=='EXPLAIN' OR self::show_explain();
                $num_rows = 0;
                foreach  (self::$result AS $row){
                    self::$last_result[$num_rows] = $row;
                    $num_rows++;
                }
                // Log number of rows the query returned
                self::$num_rows = $num_rows;

                // Return number of rows selected
                $return_val = $num_rows;
            }
        }
        // @mysql_free_result(self::$result);
        self::$result = null;

        return $return_val;
    }
    public static function get($output = OBJECT) {
        if ( $output == OBJECT ) {
            return self::$result->fetch(PDO::FETCH_OBJ);
        }else{
            return self::$result->fetch(PDO::FETCH_ASSOC);
        }
    }

    public static function server_info() {
        return null;
    }

    //  Get SQL/DB error.
    public static function get_error() {
        self::$last_error = self::$link->errorInfo();
        return self::$last_error;
    }
}

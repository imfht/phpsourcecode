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
        extension_loaded('mysql') OR self::print_error('mysql extension is missing. Please check your PHP configuration');
        self::$link = @mysql_connect(self::$config['HOST'].':'.self::$config['PORT'], self::$config['USER'], self::$config['PASSWORD'],iPHP_DB_NEW_LINK);
        self::$link OR self::print_error('Connect Error');
    }
    public static function ping() {
        return mysql_ping(self::$link);
    }
    public static function pre_set() {
        self::query("SET NAMES '".self::$config['CHARSET']."'");
        self::query("SET @@sql_mode = ''");
    }
    public static function select_db($flag=false) {
        $sel = @mysql_select_db(self::$config['DB'], self::$link);
        if($flag) return $sel;
        $sel OR self::print_error('Select Db Error');
    }
    // ==================================================================
    /** Quote string to use in SQL
    * @param string
    * @return string escaped string enclosed in '
    */
    public static function quote($string) {
        return "'" . mysql_real_escape_string($string, self::$link) . "'";
    }

    //  Basic Query - see docs for more detail
    public static function query($query,$QT=NULL) {
        $query = self::pre_query($query);
        if($query===false) return false;

        $return_val = 0;
        self::$result = @mysql_query($query, self::$link);

        if(!self::$result){
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
            $rows_affected = mysql_affected_rows(self::$link);
            // Take note of the insert_id
            if (in_array($QH,array("INSERT","REPLACE"))) {
                self::$insert_id = mysql_insert_id(self::$link);
            }
            // Return number of rows affected
            $return_val = $rows_affected;
        } else {
            if($QT=="field") {
                $i = 0;
                while ($i < @mysql_num_fields(self::$result)) {
                    self::$col_info[$i] = mysql_fetch_field(self::$result);
                    $i++;
                }
            }else {
                $QH=='EXPLAIN' OR self::show_explain();
                $num_rows = 0;
                while ( $row = @mysql_fetch_object(self::$result) ) {
                    self::$last_result[$num_rows] = $row;
                    $num_rows++;
                }
                // Log number of rows the query returned
                self::$num_rows = $num_rows;

                // Return number of rows selected
                $return_val = $num_rows;
            }
        }
        @mysql_free_result(self::$result);
        self::$result = null;

        return $return_val;
    }
    public static function get($output = OBJECT) {
        if ( $output == OBJECT ) {
            return mysql_fetch_object(self::$result,MYSQL_ASSOC);
        }else{
            return mysql_fetch_array(self::$result,MYSQL_ASSOC);
        }
    }

    public static function server_info() {
        return @mysql_get_server_info(self::$link);
    }

    //  Get SQL/DB error.
    public static function get_error() {
        if(is_bool(self::$link)){
            self::$last_error = mysql_error();
        }else{
            self::$last_error = mysql_error(self::$link);
        }
        return self::$last_error;
    }
}

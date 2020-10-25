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

        extension_loaded('mysqli') OR self::print_error('mysqli extension is missing. Please check your PHP configuration');
        self::$link = @new mysqli(self::$config['HOST'], self::$config['USER'], self::$config['PASSWORD'],null,self::$config['PORT']);
        self::$link->connect_errno && self::print_error('Connect Error ('.self::$link->connect_errno.') '.self::$link->connect_error);
    }
    public static function ping() {
        return self::$link->ping();
    }
    public static function pre_set() {
        self::$link->set_charset(self::$config['CHARSET']);
        self::$link->query("SET @@sql_mode =''");
    }
    public static function select_db($flag=false) {
        $sel = self::$link->select_db(self::$config['DB']);
        if($flag) return $sel;
        $sel OR self::print_error('Select Db Error ('.self::$link->errno.') '.self::$link->error);
    }
    // ==================================================================
    /** Quote string to use in SQL
    * @param string
    * @return string escaped string enclosed in '
    */
    public static function quote($string) {
        return "'" . self::$link->real_escape_string($string) . "'";
    }

    //  Basic Query - see docs for more detail
    public static function query($query,$QT=NULL) {
        $query = self::pre_query($query);
        if($query===false) return false;

        $return_val = 0;
        $result = self::$link->real_query($query);

        if(!$result){
            // If there is an error then take note of it..
            return self::print_error();
        }
        if(strpos($query,'EXPLAIN')===false){
            self::$num_queries++;
            self::$show_trace && self::backtrace($query);
        }

        self::$show_trace && self::timer_start();

	   if($QT=='get') return $result;

        $QH = strtoupper(substr($query,0,strpos($query, ' ')));
        if (in_array($QH,array('INSERT','DELETE','UPDATE','REPLACE','SET','CREATE','DROP','ALTER'))) {
            // Take note of the insert_id
            if (in_array($QH,array("INSERT","REPLACE"))) {
                self::$insert_id = self::$link->insert_id;
            }
            // Return number of rows affected
            $return_val = self::$link->affected_rows;
        } else {
            $store = self::$link->store_result();

            if($QT=="field") {
                self::$col_info = $store->fetch_fields();
            }else {
                $QH=='EXPLAIN' OR self::show_explain();
                $num_rows = 0;
                if($store){
                    while ( $row = $store->fetch_object() ) {
                        self::$last_result[$num_rows] = $row;
                        $num_rows++;
                    }
                    // $store->close();
                    $store->free();
                }
                $store = null;
                // Log number of rows the query returned
                self::$num_rows = $num_rows;

                // Return number of rows selected
                $return_val = $num_rows;
            }
        }
        $result = null;

        return $return_val;
    }
    public static function get($output = OBJECT) {
        $store = self::$link->store_result();
        if ( $output == OBJECT ) {
            return $store->fetch_object(MYSQL_ASSOC);
        }else{
            return $store->fetch_array(MYSQL_ASSOC);
        }
    }

    public static function server_info() {
        self::$link OR self::connect();
        return self::$link->server_info;
    }

    //  Get SQL/DB error.
    public static function get_error() {
        self::$last_error = self::$link->error;
        return self::$last_error;
    }
}

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
defined('iPHP') OR exit('What are you doing?');
defined('iPHP_CORE') OR exit('What are you doing?');

define('OBJECT', 'OBJECT');
define('ARRAY_A', 'ARRAY_A');
define('ARRAY_N', 'ARRAY_N');

defined('iPHP_DB_PORT') OR define('iPHP_DB_PORT', '3306');
defined('iPHP_DB_NEW_LINK') OR define('iPHP_DB_NEW_LINK', null);

class iDataBase {
    public static $print_sql = false;
    public static $show_trace = false;
    public static $show_errors = false;
    public static $show_explain = false;
    public static $num_queries = 0;
    public static $trace_info;
    public static $last_query;
    public static $col_info;
    public static $backtrace;
    public static $func_call;
    public static $last_result;
    public static $last_error ;
    public static $num_rows;
    public static $insert_id;
    public static $link;
    public static $config = null;
    public static $dbFlag = 'iPHP_DB';

    public static $time_start;
    public static $result;

    public static function config($config=null) {
        empty(static::$config) && static::$config = array(
            'HOST'       => iPHP_DB_HOST,
            'USER'       => iPHP_DB_USER,
            'PASSWORD'   => iPHP_DB_PASSWORD,
            'DB'         => iPHP_DB_NAME,
            'CHARSET'    => iPHP_DB_CHARSET,
            'PORT'       => iPHP_DB_PORT,
            'PREFIX'     => iPHP_DB_PREFIX,
            'PREFIX_TAG' => iPHP_DB_PREFIX_TAG
        );
        $config && static::$config = $config;
    }
    public static function connect($flag=null) {
        static::config();
        if(isset($GLOBALS[static::$dbFlag])){
            static::$link = $GLOBALS[static::$dbFlag];
            if(static::$link){
                if(static::ping())
                    return static::$link;
            }
        }
        static::link();
        if($flag==='link'){
            return static::$link;
        }
        $GLOBALS[static::$dbFlag] = static::$link;
        static::pre_set();
        if($flag===null){
            static::select_db();
        }
    }
    // public static function link() {
    //     var_dump('expression');
    // }
    public static function ping() {}
    public static function pre_set() {}
    public static function select_db() {}
    public static function quote($string) {}

    public static function table($name) {
        static::config();
        return static::$config['PREFIX'].str_replace(static::$config['PREFIX_TAG'],'', trim($name));
    }
    public static function check_table($table,$prefix=true) {
        $prefix && $table = static::table($table);
        $variable = static::tables_list();
        foreach ($variable as $key => $value) {
            $tables_list[$value['TABLE_NAME']] = true;
        }
        $table = strtolower($table);
        if($tables_list[$table]){
            return true;
        }
        return false;
    }
    /** Get tables list
    * @return array array($name => $type)
    */
    public static function tables_list() {
        return iDB::all(iDB::version() >= 5
            ? "SELECT TABLE_NAME, TABLE_TYPE FROM `information_schema`.`TABLES` WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME"
            : "SHOW TABLES"
        );
    }
    public static function pre_query($query) {
        if(empty($query)){
            if (static::$show_errors) {
                static::print_error("SQL IS EMPTY");
            } else {
                return false;
            }
        }
        if(static::$print_sql){
            echo '<pre>';
            print_r($query);
            echo '</pre>';
            return false;
        }
        static::$link OR static::connect();

        // filter the query, if filters are available
        // NOTE: some queries are made before the plugins have been loaded, and thus cannot be filtered with this method
        $query  = str_replace(static::$config['PREFIX_TAG'],static::$config['PREFIX'], trim($query));

        static::flush();

        // Log how the function was called
        static::$func_call = __CLASS__.'::query("'.$query.'")';

        // Keep track of the last query for debug..
        static::$last_query = $query;

        // Perform the query via std mysql_query function..
        static::$show_trace && static::timer_start();

        // $query = static::quote($query);
        return $query;
    }
    //  Basic Query - see docs for more detail
    public static function query($query,$QT=NULL) {
    }

    public static function get($output = OBJECT) {
    }
    /**
     * Insert an array of data into a table
     * @param string $table WARNING: not sanitized!
     * @param array $data should not already be SQL-escaped
     * @return mixed results of static::query()
     */
    public static function insert($table, $data,$IGNORE=false) {
        $fields = array_keys($data);
        $fields = static::field($fields);
        static::query("INSERT ".($IGNORE?'IGNORE':'')." INTO `".static::table($table)."` (`" . implode('`,`',$fields) . "`) VALUES ('".implode("','",$data)."')");
        return static::$insert_id;
    }
    public static function insert_multi($table,$data,$IGNORE=false,$fields=null) {
        $datasql = array();
        foreach ((array)$data as $key => $d) {
            $fields===null && $fields = array_keys($d);
            $datasql[]= "('".implode("','",$d)."')";
        }
        if($datasql){
            $fields = static::field($fields);
            return static::query("INSERT ".($IGNORE?'IGNORE':'')." INTO `".static::table($table)."` (`" . implode('`,`',$fields) . "`) VALUES ".implode(',',$datasql));
        }
    }
    /**
     * Update a row in the table with an array of data
     * @param string $table WARNING: not sanitized!
     * @param array $data should not already be SQL-escaped
     * @param array $where a named array of WHERE column => value relationships.  Multiple member pairs will be joined with ANDs.  WARNING: the column names are not currently sanitized!
     * @return mixed results of static::query()
     */
    public static function update($table, $data, $where) {
        $bits = $wheres = array();
        foreach ( array_keys($data) as $k ){
            $bits[] = "`".static::field($k)."` = '$data[$k]'";
        }
        if ( is_array( $where ) ){
            foreach ( $where as $c => $v )
                $wheres[] = "`".static::field($c)."` = '" . addslashes( $v ) . "'";
        }else{
            return false;
        }
        return static::query("UPDATE `".static::table($table)."` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres ) );
    }
    public static function delete($table, $where) {
        $wheres = array();
        if ( is_array( $where ) ){
            foreach ( $where as $c => $v )
                $wheres[] = static::field($c)." = '" . addslashes( $v ) . "'";
        }else{
            return false;
        }
        return static::query("DELETE FROM `".static::table($table)."` WHERE " . implode( ' AND ', $wheres ));
    }
    /**
     * Get one variable from the database
     * @param string $query (can be null as well, for caching, see codex)
     * @param int $x = 0 row num to return
     * @param int $y = 0 col num to return
     * @return mixed results
     */
    public static function val($table, $field, $where) {
        $fields = $wheres = array();
        if ( is_array( $field ) ){
            foreach ( $field as $c => $f )
                $fields[] = "`$f`";
        }

        $fields = static::field($fields);

        if ( is_array( $where ) ){
            foreach ( $where as $c => $v ){
                if(strpos($c,'!')===false){
                    $wheres[] = static::field($c)."= '" . addslashes( $v ) . "'";
                }else{
                    $c = str_replace('!', '', $c);
                    $wheres[] = static::field($c)."!= '" . addslashes( $v ) . "'";
                }
            }
        }
        if($fields && $wheres){
            return static::value("SELECT ".implode( ', ', $fields )." FROM `".static::table($table)."` WHERE " . implode( ' AND ', $wheres ) . ' LIMIT 1;' );
        }else{
            return false;
        }
    }
    public static function value($query=null, $x = 0, $y = 0) {
        static::$func_call = __CLASS__."::value(\"$query\",$x,$y)";
        $query && static::query($query);
        // Extract var out of cached results based x,y vals
        if ( !empty( static::$last_result[$y] ) ) {
            $values = array_values(get_object_vars(static::$last_result[$y]));
        }
        // If there is a value return it else return null
        return (isset($values[$x]) && $values[$x]!=='') ? $values[$x] : null;
    }

    /**
     * Get one row from the database
     * @param string $query
     * @param string $output ARRAY_A | ARRAY_N | OBJECT
     * @param int $y row num to return
     * @return mixed results
     */
    public static function row($query = null, $output = OBJECT, $y = 0) {
        static::$func_call = __CLASS__."::row(\"$query\",$output,$y)";
        $query && static::query($query);

        if ( !isset(static::$last_result[$y]) )
            return null;

        if ( $output == OBJECT ) {
            return static::$last_result[$y] ? static::$last_result[$y] : null;
        } elseif ( $output == ARRAY_A ) {
            return static::$last_result[$y] ? get_object_vars(static::$last_result[$y]) : null;
        } elseif ( $output == ARRAY_N ) {
            return static::$last_result[$y] ? array_values(get_object_vars(static::$last_result[$y])) : null;
        } else {
            static::print_error(__CLASS__."::row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
        }
    }

    /**
     * Return an entire result set from the database
     * @param string $query (can also be null to pull from the cache)
     * @param string $output ARRAY_A | ARRAY_N | OBJECT
     * @return mixed results
     */
    public static function all($query = null, $output = ARRAY_A) {
        static::$func_call = __CLASS__."::all(\"$query\", $output)";

        $query && static::query($query);

        // Send back array of objects. Each row is an object
        if ( $output == OBJECT ) {
            return static::$last_result;
        } elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
            if ( static::$last_result ) {
                $i = 0;
                foreach( (array) static::$last_result as $row ) {
                    if ( $output == ARRAY_N ) {
                        // ...integer-keyed row arrays
                        $new_array[$i] = array_values( get_object_vars( $row ) );
                    } else {
                        // ...column name-keyed row arrays
                        $new_array[$i] = get_object_vars( $row );
                    }
                    ++$i;
                }
                return $new_array;
            } else {
                return array();
            }
        }
    }

    /**
     * Gets one column from the database
     * @param string $query (can be null as well, for caching, see codex)
     * @param int $x col num to return
     * @return array results
     */
    public static function col($query = null , $x = 0) {
        $query && static::query($query);
        $new_array = array();
        // Extract the column values
        for ( $i=0; $i < count(static::$last_result); $i++ ) {
            $new_array[$i] = static::value(null, $x, $i);
        }
        return $new_array;
    }

    /**
     * Grabs column metadata from the last query
     * @param string $info_type one of name, table, def, max_length, not_null, primary_key, multiple_key, unique_key, numeric, blob, type, unsigned, zerofill
     * @param int $col_offset 0: col name. 1: which table the col's in. 2: col's max length. 3: if the col is numeric. 4: col's type
     * @return mixed results
     */
    public static function col_info($query = null ,$info_type = 'name', $col_offset = -1) {
        $query && static::query($query,"field");
        if ( static::$col_info ) {
            if ( $col_offset == -1 ) {
                $i = 0;
                foreach(static::$col_info as $col ) {
                    $new_array[$i] = $col->{$info_type};
                    $i++;
                }
                return $new_array;
            } else {
                return static::$col_info[$col_offset]->{$info_type};
            }
        }
    }
	public static function field($string) {
        return is_array($string) ?
            array_map("iDB::field", $string) :
            preg_replace('/[^a-zA-Z0-9_\-`]/is','',$string);
    }

    public static function server_info() {

    }

    public static function version() {
        static::$link OR static::connect();
        $version = preg_replace('|[^0-9\.]|', '', static::server_info());
        if(strtolower(iPHP_DB_TYPE)!=="mysql"){
            return $version;
        }
        if ( version_compare($version, '4.0.0', '<') ){
            static::print_error('mysql version error,iPHP requires MySQL 4.0.0 or higher');
        }else{
            return $version;
        }
    }

    // ==================================================================
    //  Kill cached query results

    public static function flush() {
        static::$last_result  = array();
        static::$col_info     = null;
        static::$last_query   = null;
    }
    /**
     * Starts the timer, for debugging purposes
     */
    public static function timer_start() {
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        static::$time_start = $mtime[1] + $mtime[0];
        return true;
    }

    /**
     * Stops the debugging timer
     * @return int total time spent on the query, in milliseconds
     */
    public static function timer_stop($restart=false) {
        $mtime      = microtime();
        $mtime      = explode(' ', $mtime);
        $time_end   = $mtime[1] + $mtime[0];
        $time_total = $time_end - static::$time_start;
        $restart && static::$time_start = $time_end;
        return round($time_total, 5);
    }
    // ==================================================================
    public static function show_explain(){
        if(!static::$show_explain) return;
        $query = static::$last_query;
        $explain = static::row('EXPLAIN EXTENDED '.$query);
        $explain && $explain->query = $query;
        if(static::$show_explain=='print'){
            echo "<pre>".
            var_dump($explain);
            echo "</pre>";
        }else{
            echo "<!--\n";
            print_r($explain);
            echo "-->\n";
        }
    }

    //  Print SQL/DB error.
    public static function get_error() {
    }
    public static function print_error($error = '') {
        if(!static::$show_errors) return;

        static::get_error();
        $error OR $error  = static::$last_error;
        if ($error) {
            $message = "<strong>iDB error:</strong> {$error} [".static::$last_error."]<br /><code>".static::$last_query."</code>";
            trigger_error($message,E_USER_ERROR);
        } else {
            return false;
        }
    }
    public static function backtrace($query){
        $trace = '';
        $backtrace = debug_backtrace();
        // $backtrace = array_slice($backtrace,1,2);
        foreach ($backtrace as $i => $l) {
            $trace .= "\n[$i] in function <b>{$l['class']}{$l['type']}{$l['function']}</b>";
            $l['file'] = str_replace('\\', '/', $l['file']);
            $l['file'] = iSecurity::filter_path($l['file']);
            $l['file'] && $trace .= " in <b>{$l['file']}</b>";
            $l['line'] && $trace .= " on line <b>{$l['line']}</b>";
        }
        static::$trace_info[] = array('sql'=>$query, 'exec_time'=>static::timer_stop(true),'backtrace'=>$trace);
        unset($trace,$backtrace);
    }
}

if(strtolower(iPHP_DB_TYPE)==="sqlite"){
    // require_once iPHP_CORE.'/DB/SQLite.class.php';//还有很多问题暂时不能用
}elseif(strtolower(iPHP_DB_TYPE)==="pgsql"){
    // require_once iPHP_CORE.'/DB/PgSQL.class.php';
}else{
    if(version_compare(PHP_VERSION,'5.5','>=') && extension_loaded('mysqli')){
        require_once iPHP_CORE.'/DB/Mysqli.class.php';
    }elseif(extension_loaded('mysql')){
        require_once iPHP_CORE.'/DB/Mysql.class.php';
    }else{
        trigger_error('您的 PHP 环境看起来缺少 MySQL 数据库支持扩展。',E_USER_ERROR);
    }
}


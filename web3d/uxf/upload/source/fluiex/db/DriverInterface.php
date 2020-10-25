<?php

namespace fluiex\db;

interface DriverInterface
{
    
    public function set_config($config);
    public function connect();
    public function close();
    public function table_name($tablename);
    public function fetch_array($query, $result_type = MYSQL_ASSOC);
    public function fetch_first($sql);
    public function fetch_row($query);
    public function fetch_fields($query);
    public function result_first($sql);
    public function result($query, $row = 0);
    public function insert_id();
    public function affected_rows();
    public function query($sql, $silent = false, $unbuffered = false);
    public function free_result($query);
    public function num_rows($query);
    public function error();
    public function errno();
}


<?php
namespace home;

class HomeConst
{
    
    const DB_CONFIG = array(
        "default" => [
            "dsn" => "mysql:dbname=default;host=localhost;charset=utf8",
            "user" => "root",
            "password" => "",
            "tables" => [],
        ],
    );
    
    const ROUTERS = [];
}
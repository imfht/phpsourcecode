<?php 
/*
 * 多数据源配置档
 * 第一维下标代表数据源名称
 * 此数据源不与默认数据配置相同
 */
$DataBase['test']['host']="127.0.0.1";  #数据库地址
$DataBase['test']['user']="root"; #数据库用户名
$DataBase['test']['password']=""; #数据库密码
$DataBase['test']['name']="testdb"; #数据库名
$DataBase['test']['ut']="utf8"; #数据库编码
$DataBase['test']['first_name']="61_"; #数据库表名 前缀
/*
 * 第二个数据源,理论支持无数个
 */
$DataBase['test2']['host']="127.0.0.1";  #数据库地址
$DataBase['test2']['user']="root"; #数据库用户名
$DataBase['test2']['password']=""; #数据库密码
$DataBase['test2']['name']="testdb2"; #数据库名
$DataBase['test2']['ut']="utf8"; #数据库编码
$DataBase['test2']['first_name']=""; #数据库表名 前缀
?>
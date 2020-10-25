<?php

interface Dialect {
    public function convert($identifier); // 数据库底层名称大小写转换
    public function create_table(); // 创建新表的模板，至少包含自增长字段、创建时间、更新时间三个字段
    public function sequence($table_name); // 自增长字段名称
    public function database();            // 获取当前数据库名的SQL
    public function tables();              // 获取所有表名的SQL
    public function columns();             // 获得所有列名的SQL
}

class MySQLDialect implements Dialect {
    public function convert($identifier) {
        return $identifier;
    }

    public function create_table() {
        return 'create table if not exists %s (id integer auto_increment primary key, %s, created_at timestamp default current_timestamp, updated_at datetime)';
    }

    public function sequence($table_name) {
        return 'id';
    }

    public function database() {
        return 'select database()';
    }

    public function tables() {
        return 'select table_name from information_schema.tables where table_schema = ?';
    }

    public function columns() {
        return 'select column_name from information_schema.columns where table_schema = ? and table_name = ? order by ordinal_position';
    }
}

class PostgreSQLDialect implements Dialect {
    public function convert($identifier) {
        return strtolower($identifier);
    }

    public function create_table() {
        return 'create table if not exists %s (id serial primary key, %s, created_at timestamp default current_timestamp, updated_at timestamp default current_timestamp)';
    }

    public function sequence($table_name) {
        return "{$table_name}_id_seq";
    }

    public function database() {
        return 'select current_database()';
    }

    public function tables() {
        return "select table_name from information_schema.tables where table_schema = 'public' and table_catalog = ?";
    }

    public function columns() {
        return "select column_name from information_schema.columns where table_schema != 'information_schema' and table_catalog = ? and table_name = ?";
    }
}

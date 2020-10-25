<?php

namespace herosphp\gmodel\utils;

/**
 * 创建数据库，数据表
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
class DBFactory {

    /**
     * @var MySQLi
     */
    private static $CONN = null; //数据库连接

    private static $DB_CONFIGS = array(); //数据库连接配置

    /**
     * 默认值映射
     * @var array
     */
    private static $DEFAULT_VALUE_KEYWORD = array(
        "CURRENT_TIMESTAMP", "NULL"
    );

    /**
     * @param $options创建数据库
     */
    public static function createDatabase($options) {

        if ( !isset($options['dbname']) ) return tprintError("Error : --dbname is needed.");
        if ( !isset($options['dbhost']) ) $options['dbhost'] = '127.0.0.1';
        if ( !isset($options['dbuser']) ) $options['dbuser'] = 'root';
        if ( !isset($options['dbpass']) ) $options['dbpass'] = '123456';
        if ( !isset($options['charset']) ) $options['charset'] = 'utf8';

        self::$DB_CONFIGS = $options; //初始化数据库配置

        $sql = "CREATE DATABASE IF NOT EXISTS `{$options["dbname"]}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        if ( self::query($sql) !== false ) {
            tprintOk("create database '{$options['dbname']}' successfully.");
        } else {
            tprintError("Error : creeat database faild.");
        }

        //生成数据库配置文档
        $dbConfigFile = APP_PATH."configs/env/".ENV_CFG."/db.config.php";
        if ( !file_exists($dbConfigFile) ) {
            $tempContent = file_get_contents(dirname(__DIR__)."/template/db.config.tpl");
            if ( $tempContent != "" ) {
                $content = str_replace("{db_host}", $options["dbhost"], $tempContent);
                $content = str_replace("{db_user}", $options["dbuser"], $content);
                $content = str_replace("{db_name}", $options["dbname"], $content);
                $content = str_replace("{db_pass}", $options["dbpass"], $content);
                $content = str_replace("{db_charset}", $options["charset"], $content);

                if ( file_put_contents($dbConfigFile, $content) !== false ) {
                    tprintOk("create db config file '{$dbConfigFile}' successfully！");
                } else {
                    tprintError("create db config file '{$dbConfigFile}' faild!");
                }
            }
        } else {
            tprintWarning("config file '{$dbConfigFile}' has existed, skiped.");
        }
    }

    /**
     * @param SimpleHtmlDom $xml
     * 创建数据库结构
     */
    public static function createTables($xml) {

        $root = $xml->find("root", 1);
        $configs = array(
            "dbhost" => $root->dbhost,
            "dbuser" => $root->getAttribute("dbuser"),
            "dbpass" => $root->getAttribute("dbpass"),
            "dbname" => $root->getAttribute("dbname"),
            "charset" => $root->getAttribute("charset")
        );

        self::$DB_CONFIGS = $configs; //初始化数据库配置
        self::query("USE `{$configs["dbname"]}`;"); //选择数据库

        $tables = $xml->find("table");
        foreach ( $tables as $value ) {
            $tableName = $value->name;
            self::query("DROP TABLE IF EXISTS `{$tableName}`");
            $sql = "CREATE TABLE `{$tableName}`(";
            $pk = $value->find("pk", 0);
            if ( $pk ) {
                $sql .= "`{$pk->name}` {$pk->type} NOT NULL ";
                if ( $pk->ai ) {
                    $sql .= "AUTO_INCREMENT ";
                }
                $sql .= "COMMENT '主键',";
            }

            //添加字段
            $fields = $value->find("fields", 0);
            if ( $fields ) {
                foreach( $fields->children() as $fd ) {
                    if ( $fd->default || $fd->default === "0" || $fd->default === '' ) {   //has default value
                        if ( in_array($fd->default, self::$DEFAULT_VALUE_KEYWORD) ) {
                            $sql .= "`{$fd->name}` {$fd->type} DEFAULT {$fd->default} COMMENT '{$fd->comment}',";
                        } else {
                            $sql .= "`{$fd->name}` {$fd->type} DEFAULT '{$fd->default}' COMMENT '{$fd->comment}',";
                        }
                    } else { //has not default value
                        $sql .= "`{$fd->name}` {$fd->type} DEFAULT NULL COMMENT '{$fd->comment}',";
                    }

                    //创建索引
                    if ( $fd->getAttribute("add-index") == "true" ) {
                        $indexType = $fd->getAttribute("index-type");
                        if ( $indexType == "normal" ) {
                            $sql .= "KEY `{$fd->name}` (`{$fd->name}`), ";
                        } elseif ( $indexType == "unique" ) {
                            $sql .= "UNIQUE KEY `{$fd->name}` (`{$fd->name}`),";
                        }
                    }
                }
            }

            if ( $pk ) $sql .= "PRIMARY KEY (`{$pk->name}`)";
            if ( !$value->engine ) {
                $value->engine = "InnoDB";
            }
            $sql .= ") ENGINE={$value->engine}  DEFAULT CHARSET={$configs['charset']} COMMENT='{$value->comment}' AUTO_INCREMENT=1 ;";

            if ( self::query($sql) !== false ) {
                tprintOk("create table '{$tableName}' successfully.");

            } else {
                tprintError("create table '{$tableName}' faild.");
                tprintError(self::$CONN->error);
            }

        }

    }

    /**
     * 连接数据库
     * @param $configs
     */
    protected static function connect($configs) {
        try {
            self::$CONN = mysqli_connect($configs["dbhost"], $configs["dbuser"], $configs["dbpass"]);
            if ( !self::$CONN ) {
                tprintError("Error : can not to connect to the database.");
                return;
            }
        } catch(\Exception $e) {
            tprintError($e->getMessage());
        }
        self::$CONN->query("SET names {$configs["charset"]}");
        self::$CONN->query("SET character_set_client = {$configs["charset"]}");
        self::$CONN->query("SET character_set_results = {$configs["charset"]}");
    }

    /**
     * 执行查询
     * @param $sql
     * @return mixed
     */
    private static function query($sql) {
        if ( !self::$CONN ) {
            self::connect(self::$DB_CONFIGS);
        }
        printLine($sql); //打印sql语句
        return self::$CONN->query($sql);
    }

}

<?php
return array(
    /**
     * 数据库配置
     * 'dbhost'=>'localhost', //主机
     * 'dbuser'=>'root',      //数据库用户名
     * 'dbpsw'=>'liyongsheng',//数据库密码
     * 'prefix'=>'templi_',   //表前缀
     * 'dbdrive'=>'pdo_mysql', //数据库驱动
     */
    'master'=> array(
            'dbhost'=>'localhost',
            'dbuser'=>'root',
            'dbpsw'=>'liyongsheng',
            'dbname'=>'templi',
            'prefix'=>'templi_',
            'dbdrive'=>'Pdo_mysql',
            'charset'=>'utf8'
        ),
    'slave'=>array(
            'dbhost'=>'localhost',
            'dbuser'=>'root',
            'dbpsw'=>'liyongsheng',
            'dbname'=>'templi2',
            'prefix'=>'templi_',
            'dbdrive'=>'Pdo_mysql',
            'charset'=>'utf8'
        )
);

?>
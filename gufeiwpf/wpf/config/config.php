<?php




return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'wpf',
        'charset'     => 'utf8',
    ),
    'Masterdatabase' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'wpf',
        'charset'     => 'utf8',
    ),
    'Slavedatabase' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'wpf',
        'charset'     => 'utf8',
    ),
    
    
    'cache_lifttime'    =>  array(
        "lifetime" => 86400
    ),
    
    'memcache'  =>  array(
        "host" => "localhost",
        "port" => "11211"
    ),
    
    'memcached'  =>  array(
        'servers'   =>  array(
            array(
                    "host" => "127.0.0.1",
                    "port" => "11211",
                    "weight" => "1"
            ),
        ),
    ),
    
    'redis' =>  array(
        "host" => "192.168.6.100",
        "port" => "6379"
    ),
    
    'filecache' => array(
        "cacheDir" => CACHE_PATH."/filecache/",
        "prefix"   => 'filecache'
    ),
    
    'modelsMetadata' => array(
        'metaDataDir' => CACHE_PATH.'/metadata/',
        "lifetime" => 86400,
        "prefix"   => "modelsMetadata"
    ),
    
    
    'cryptKey' => "%31.1e$i86e$f!8jzaaa$w3",
    
    'cookie_name' => array(
        'WPF_ADMIN_AUTH' => "wpf_admin_auth"
    ),
    
    "ADMIN_ADMINISTRATOR" => array(
        1,2
    ),
    
    "DEVELOP_MODE" => false,
    
));
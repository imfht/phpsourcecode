<?php

/**
 * 缓存配置
 */
return array(
    'default_type' => 'file', //默认的缓存类型，值是下面drivers关联数组的键名称。
    'drivers' => array(
	//自定义缓存示例
//	'my_cache' => array(
//	    'class' => '\Cache_MyCache', //缓存类名称
//	    'config' => null//需要传递给缓存类构造方法的第一个参数，一般是配置信息数组，不需要就保持null
//	),
	'file' => array(
	    'class' => '\Soter_Cache_File',
	    //缓存文件保存路径
	    'config' => \Sr::config()->getStorageDirPath() . 'cache/'
	),
	'memcache' => array(
	    'class' => '\Soter_Cache_Memcache',
	    'config' => array(//memcache服务器信息，支持多个
		array("127.0.0.1", 11211),
	    //array("new.host.ip",11211),
	    )
	),
	'memcached' => array(
	    'class' => '\Soter_Cache_Memcached',
	    'config' => array(//memcached服务器信息，支持多个
		array("127.0.0.1", 11211),
	    //array("new.host.ip",11211),
	    )
	),
	'apc' => array(
	    'class' => '\Soter_Cache_Apc',
	    'config' => NULL//apc缓存不需要配置信息，保持null即可
	),
	'redis_cluster' => array(
	    'class' => '\Soter_Cache_Redis_Cluster',
	    'config' => array(
		'hosts'=>array(//集群中所有master主机信息
		    //'127.0.0.1:7001',
		    //'127.0.0.1:7002',
		    //'127.0.0.1:7003',
		),
		'timeout'=>1.5,//连接超时，单位秒
		'read_timeout'=>1.5,//读超时，单位秒
		'persistent'=>false,//是否持久化连接
		//key的前缀，便于管理查看，在set和get的时候会自动加上和去除前缀，无前缀请保持null
		'prefix' => null, //Sr::server('HTTP_HOST')
	    )
	),
	'redis' => array(
	    'class' => '\Soter_Cache_Redis',
	    'config' =>
	    array(
		//redis服务器信息，支持集群。
		//原理是：读写的时候根据算法sprintf('%u',crc32($key))%count($nodeCount)
		//把$key分散到下面不同的master服务器上，负载均衡，而且还支持单个key的主从负载均衡。
		array(
		    'master' => array(
			//sock,tcp;连接类型，tcp：使用host port连接，sock：本地sock文件连接
			'type' => 'tcp',
			//key的前缀，便于管理查看，在set和get的时候会自动加上和去除前缀，无前缀请保持null
			'prefix' => null, //Sr::server('HTTP_HOST')
			//type是sock的时候，需要在这里指定sock文件的完整路径
			'sock' => '',
			//type是tcp的时候，需要在这里指定host，port，password，timeout，retry
			//主机地址
			'host' => '127.0.0.1',
			//端口
			'port' => 6379,
			//密码，如果没有,保持null
			'password' => NULL,
			//0意味着没有超时限制，单位秒
			'timeout' => 3000,
			//连接失败后的重试时间间隔，单位毫秒
			'retry' => 100,
			// 数据库序号，默认0, 参考 http://redis.io/commands/select
			'db' => 0,
		    ),
		    'slaves' => array(
//			array(
//			    'type' => 'tcp',
//			    'prefix' => null, //Sr::server('HTTP_HOST')
//			    'sock' => '',
//			    'host' => '127.0.0.1',
//			    'port' => 6380,
//			    'password' => NULL,
//			    'timeout' => 3000,
//			    'retry' => 100,
//			    'db' => 0,
//			),
		    )
		),
//		array(
//		    'master' => array(
//			'type' => 'tcp',
//			'prefix' => null,
//			'sock' => '',
//			'host' => '10.69.112.34',
//			'port' => 6379,
//			'password' => NULL,
//			'timeout' => 3000,
//			'retry' => 100,
//			'db' => 0,
//		    ),
//		    'slaves' => array(
//		    )
//		),
	    )
	),
    )
);

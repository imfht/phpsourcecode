<?php

/**
 * 需要缓存的控制器方法缓存配置
 * 键规则是：控制器名称::方法名称
 * 控制器名称不需要Controller_前缀,方法名称不需要前缀。
 */
return array(
    'Welcome::index' => array(
	'cache' => true, //是否开启缓存
	'time' => 3600, //缓存时间，单位秒
	'key' => function() {
		//根据具体的业务逻辑，返回缓存key，
		//返回的key如果为空，则不使用缓存
		return 'userId:' . \Sr::get('userId');
	}
    )
);

<?php 
# 这个文件是用来改写 conf 配置的,全局用的 $conf 变量或者core::$conf 都可以读取到
# 当你有多个域名时，直接改写 control_path / model_path / view_path
# 甚至修改 db 或者 cache 配置都是可以的
# 就可以通过最高的效率，在同一个项目中无缝切换多个站点
# 用于站群模式，同时也适用于开发和线上环境自由切换
return array(
	'aaa' => 1,
);
	
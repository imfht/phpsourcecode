<?php  
define ('APP_NAME', '' );//也可不定义项目名称，直接将各功能目录与index.php放在一起
define ('ROOT', dirname(__FILE__));//index.php所在目录
require_once( '../Framk/Framk.class.php');//用户可根据Framk框架目录与index.php的目录关系来加载框架
new Framk();
?>

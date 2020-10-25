<?php 
//基础常量
define("DS" , "/");
define("_SITE_HOST","http://" . $_SERVER['HTTP_HOST']. _HOST_ROOTPATH);
define("_HOST_ROOT",DS);
define("_HOST_ROOTPATH",DS);
define("_DEFAULT_TEMPLATE","default");
define("_DEFAULT_LANGUAGE","zh-cn");
define("_SITE_INCLUDE_PATH",_SITE_ROOT.DS."include".DS);
define("_SITE_INCLUDE_CLASS_PATH",_SITE_ROOT.DS."include".DS."class".DS);

define('_WEB_INCLUDE_PATH', _HOST_ROOT."include" . DS);
define("_SITE_CACHE",_SITE_ROOT.DS."cache".DS);
define("_SITE_APPLICATION_PATH",_SITE_ROOT.DS."application".DS);
define("_SITE_APPLICATION_TEMPLATE",_SITE_APPLICATION_PATH."template".DS._DEFAULT_TEMPLATE.DS._DEFAULT_LANGUAGE.DS);


<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2014-11-08 03:48:44 --- CRITICAL: ErrorException [ 1 ]: Call to undefined function success() ~ APPPATH\classes\Controller\Type.php [ 19 ] in file:line
2014-11-08 03:48:44 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2014-11-08 03:49:03 --- CRITICAL: ErrorException [ 1 ]: Call to undefined function _extends() ~ APPPATH\classes\Controller\Common.php [ 28 ] in file:line
2014-11-08 03:49:03 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2014-11-08 03:49:22 --- CRITICAL: ErrorException [ 2 ]: Invalid argument supplied for foreach() ~ APPPATH\classes\Controller\Common.php [ 30 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php:30
2014-11-08 03:49:22 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(30): Kohana_Core::error_handler(2, 'Invalid argumen...', 'D:\\xampp\\htdocs...', 30, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(20): Controller_Common->_make_output('success', 'test', true, Array, true)
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Type.php(19): Controller_Common->success('test')
#3 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Type->action_gettypelist()
#4 [internal function]: Kohana_Controller->execute()
#5 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Type))
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#9 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php:30
2014-11-08 03:50:11 --- CRITICAL: ErrorException [ 1 ]: Class 'Auth' not found ~ APPPATH\classes\Controller\Common.php [ 30 ] in file:line
2014-11-08 03:50:11 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2014-11-08 03:50:24 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: msg ~ APPPATH\classes\Controller\Common.php [ 30 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php:30
2014-11-08 03:50:24 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(30): Kohana_Core::error_handler(8, 'Undefined varia...', 'D:\\xampp\\htdocs...', 30, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(20): Controller_Common->_make_output('success', 'test', true, Array, true)
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Type.php(19): Controller_Common->success('test')
#3 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Type->action_gettypelist()
#4 [internal function]: Kohana_Controller->execute()
#5 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Type))
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#9 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php:30
2014-11-08 03:50:36 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: result ~ APPPATH\classes\Controller\Common.php [ 46 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php:46
2014-11-08 03:50:36 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(46): Kohana_Core::error_handler(8, 'Undefined varia...', 'D:\\xampp\\htdocs...', 46, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(33): Controller_Common->output(Array)
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(20): Controller_Common->_make_output('success', 'test', true, Array, true)
#3 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Type.php(19): Controller_Common->success('test')
#4 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Type->action_gettypelist()
#5 [internal function]: Kohana_Controller->execute()
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Type))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#10 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php:46
2014-11-08 03:51:09 --- CRITICAL: ErrorException [ 8 ]: Undefined variable: result ~ APPPATH\classes\Controller\Common.php [ 47 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php:47
2014-11-08 03:51:09 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(47): Kohana_Core::error_handler(8, 'Undefined varia...', 'D:\\xampp\\htdocs...', 47, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(33): Controller_Common->output(Array)
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(20): Controller_Common->_make_output('success', 'test', true, Array, true)
#3 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Type.php(19): Controller_Common->success('test')
#4 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Type->action_gettypelist()
#5 [internal function]: Kohana_Controller->execute()
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Type))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#10 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php:47
2014-11-08 03:57:21 --- CRITICAL: ErrorException [ 4096 ]: Argument 2 passed to Kohana_Kohana_Exception::__construct() must be of the type array, object given, called in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php on line 32 and defined ~ SYSPATH\classes\Kohana\Kohana\Exception.php [ 50 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php:50
2014-11-08 03:57:21 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php(50): Kohana_Core::error_handler(4096, 'Argument 2 pass...', 'D:\\xampp\\htdocs...', 50, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(32): Kohana_Kohana_Exception->__construct('test', Object(Response))
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(20): Controller_Common->_make_output('success', 'test', true, Array, true)
#3 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Type.php(17): Controller_Common->success('test')
#4 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Type->action_gettypelist()
#5 [internal function]: Kohana_Controller->execute()
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Type))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#10 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php:50
2014-11-08 03:57:45 --- CRITICAL: ErrorException [ 4096 ]: Argument 2 passed to Kohana_Kohana_Exception::__construct() must be of the type array, object given, called in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php on line 32 and defined ~ SYSPATH\classes\Kohana\Kohana\Exception.php [ 50 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php:50
2014-11-08 03:57:45 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php(50): Kohana_Core::error_handler(4096, 'Argument 2 pass...', 'D:\\xampp\\htdocs...', 50, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(32): Kohana_Kohana_Exception->__construct(Array, Object(Response))
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(20): Controller_Common->_make_output('success', 'test', true, Array, true)
#3 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Type.php(17): Controller_Common->success('test')
#4 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Type->action_gettypelist()
#5 [internal function]: Kohana_Controller->execute()
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Type))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#10 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php:50
2014-11-08 03:57:47 --- CRITICAL: ErrorException [ 4096 ]: Argument 2 passed to Kohana_Kohana_Exception::__construct() must be of the type array, object given, called in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php on line 32 and defined ~ SYSPATH\classes\Kohana\Kohana\Exception.php [ 50 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php:50
2014-11-08 03:57:47 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php(50): Kohana_Core::error_handler(4096, 'Argument 2 pass...', 'D:\\xampp\\htdocs...', 50, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(32): Kohana_Kohana_Exception->__construct(Array, Object(Response))
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(20): Controller_Common->_make_output('success', 'test', true, Array, true)
#3 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Type.php(17): Controller_Common->success('test')
#4 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Type->action_gettypelist()
#5 [internal function]: Kohana_Controller->execute()
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Type))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#10 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php:50
2014-11-08 03:58:12 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected '=', expecting ')' ~ APPPATH\classes\Controller\Common.php [ 32 ] in file:line
2014-11-08 03:58:12 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2014-11-08 03:58:31 --- CRITICAL: ErrorException [ 4096 ]: Argument 2 passed to Kohana_Kohana_Exception::__construct() must be of the type array, object given, called in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php on line 32 and defined ~ SYSPATH\classes\Kohana\Kohana\Exception.php [ 50 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php:50
2014-11-08 03:58:31 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php(50): Kohana_Core::error_handler(4096, 'Argument 2 pass...', 'D:\\xampp\\htdocs...', 50, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(32): Kohana_Kohana_Exception->__construct(Array, Object(Response))
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Common.php(20): Controller_Common->_make_output('success', 'test', true, Array, true)
#3 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Type.php(17): Controller_Common->success('test')
#4 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Type->action_gettypelist()
#5 [internal function]: Kohana_Controller->execute()
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Type))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#10 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Kohana\Exception.php:50
2014-11-08 04:10:03 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected '=', expecting '&' or variable (T_VARIABLE) ~ APPPATH\classes\Controller\Common.php [ 13 ] in file:line
2014-11-08 04:10:03 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2014-11-08 04:10:29 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected ')' ~ APPPATH\classes\Controller\Common.php [ 19 ] in file:line
2014-11-08 04:10:29 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2014-11-08 04:10:41 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected '=', expecting '&' or variable (T_VARIABLE) ~ APPPATH\classes\Controller\Common.php [ 24 ] in file:line
2014-11-08 04:10:41 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2014-11-08 08:22:45 --- CRITICAL: ErrorException [ 8 ]: Undefined property: Database_Query_Builder_Select::$execute ~ APPPATH\classes\Model\Type.php [ 18 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Model\Type.php:18
2014-11-08 08:22:45 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Model\Type.php(18): Kohana_Core::error_handler(8, 'Undefined prope...', 'D:\\xampp\\htdocs...', 18, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Type.php(33): Model_Type->get_typeinfo('1')
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Type->action_typeinfo()
#3 [internal function]: Kohana_Controller->execute()
#4 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Type))
#5 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#8 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Model\Type.php:18
2014-11-08 22:36:02 --- CRITICAL: ErrorException [ 8 ]: Undefined property: Controller_Article::$model ~ APPPATH\classes\Controller\Article.php [ 20 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Article.php:20
2014-11-08 22:36:02 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Article.php(20): Kohana_Core::error_handler(8, 'Undefined prope...', 'D:\\xampp\\htdocs...', 20, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Article->action_gettypelist()
#2 [internal function]: Kohana_Controller->execute()
#3 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Article))
#4 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#7 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Article.php:20
2014-11-08 22:38:09 --- CRITICAL: ErrorException [ 2 ]: Missing argument 5 for Model_Article::get_typelist(), called in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Article.php on line 21 and defined ~ APPPATH\classes\Model\Article.php [ 6 ] in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Model\Article.php:6
2014-11-08 22:38:09 --- DEBUG: #0 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Model\Article.php(6): Kohana_Core::error_handler(2, 'Missing argumen...', 'D:\\xampp\\htdocs...', 6, Array)
#1 D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Controller\Article.php(21): Model_Article->get_typelist(NULL, 10, 0, 0)
#2 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Controller.php(84): Controller_Article->action_gettypelist()
#3 [internal function]: Kohana_Controller->execute()
#4 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Article))
#5 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#6 D:\xampp\htdocs\dedecmsapi\itxtiapi\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#7 D:\xampp\htdocs\dedecmsapi\itxtiapi\index.php(118): Kohana_Request->execute()
#8 {main} in D:\xampp\htdocs\dedecmsapi\itxtiapi\application\classes\Model\Article.php:6
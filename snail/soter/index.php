<?php

/*
 * Copyright 2017 Soter(狂奔的蜗牛 672308444@163.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Soter
 *
 * An open source application development framework for PHP 5.3.0 or newer
 *
 * @package       Soter
 * @author        狂奔的蜗牛
 * @email         672308444@163.com
 * @copyright     Copyright (c) 2015 - 2017, 狂奔的蜗牛, Inc.
 * @link          http://git.oschina.net/snail/soter
 * @since         v1.1.32
 * @createdtime   2017-03-27 16:47:15
 */
 
define("IN_SOTER", true);
/* 引入核心 */
require dirname(__FILE__) . '/soter.php';
/* 项目目录路径 */
define('SOTER_APP_PATH', \Sr::realPath(dirname(__FILE__) . '/application') . '/');
/* 项目拓展包路径 */
define('SOTER_PACKAGES_PATH', SOTER_APP_PATH . 'packages/');
/* 初始化系统配置 */
\Soter::initialize()
	/* 设置Soter异常管理程序保留的内存大小，单位byte */
	->setExceptionMemoryReserveSize(512000)
	/* 设置错误级别,也就是error_reporting()的参数,只有此级别的错误才会触发下面的错误显示控制处理类 */
	->setExceptionLevel(E_ALL ^ E_DEPRECATED)
	/* 设置Soter管理异常错误 */
	->setExceptionControl(true)
	/* 时区设置 */
	->setTimeZone('PRC')
	/* 项目目录路径 */
	->setApplicationDir(SOTER_APP_PATH)
	/* 项目存储数据目录路径，必须可写 */
	->setStorageDirPath(SOTER_APP_PATH . 'storage/')
	/* 注册项目包 */
	->addPackage(SOTER_APP_PATH)
	/* 注册拓展包 */
	->addPackages(array(
		//SOTER_PACKAGES_PATH . 'misc',
	))
	/* 注册自动加载的函数文件 */
	->addAutoloadFunctions(array(
		// 'functions'
	))
	/* 设置运行环境 ,值就是config配置目录下面的子文件夹名称,区分大小写 */
	->setEnvironment(($env = (($cliEnv = \Sr::getOpt('env')) ? $cliEnv : \Sr::arrayGet($_SERVER, 'ENVIRONMENT'))) ? $env : 'development')
	/* 系统错误显示设置，非产品环境才显示 */
	->setShowError(\Sr::config()->getEnvironment() != 'production')
	/**
	 * 下面配置中可以使用：
	 * 1.主项目的claseses目录，主项目类库目录，主项目拓展包里面的类
	 * 2.这几个目录如果存在同名类，使用的优先级高到低是：
	 *   主项目classes->类库classes->拓展包classes->拓展包类库classes
	 */
	/* 入口文件所在目录 */
	->setIndexDir(dirname(__FILE__) . '/')
	/* 入口文件名称 */
	->setIndexName(pathinfo(__FILE__, PATHINFO_BASENAME))
	/* 宕机维护模式 */
	->setIsMaintainMode(false)
	/* 宕机维护模式IP白名单 */
	//->setMaintainIpWhitelist(array('127.0.0.2', '192.168.0.2/32'))
	/* 宕机维护模式处理方法 */
	->setMaintainModeHandle(new \Soter_Maintain_Handle_Default())
	/**
	 * 如果服务器是ngix之类代理转发请求到后端apache运行的PHP。
	 * 那么这里应该设置信任的nginx所在服务器的ip。
	 * nginx里面应该设置 X_FORWARDED_FOR server变量来表示真实的客户端IP。
	 * 不然通过Sr::clientIp()是获取不到真实的客户端IP的。
	 * 参数是数组，有多个ip放入数组即可,ip也支持网段写法,比如网段192.168.1.0/24。
	 */
	//->setBackendServerIpWhitelist(array('192.168.2.2'))
	/* 初始化请求 */
	->setRequest(new \Soter_Request_Default())
	/* 网站是否开启了nginx或者apache的url“伪静态”重写，开启了这里设置为true，
	  这样Sr::url方法在生成url的时候就知道是否加上入口文件名称 */
	->setIsRewrite(false)
	/* 注册默认pathinfo路由器 */
	->addRouter(new \Soter_Router_PathInfo_Default())
	/* pathinfo路由器,注册uri重写 */
	->setUriRewriter(new \Soter_Uri_Rewriter_Default())
	/* 注册默认get路由器 */
	->addRouter(new \Soter_Router_Get_Default())
	/* get路由器,url中的控制器的get变量名 */
	->setRouterUrlControllerKey('c')
	/* get路由器,url中的方法的get变量名 */
	->setRouterUrlMethodKey('a')
	/* get路由器,url中的hmvc模块的get变量名 */
	->setRouterUrlModuleKey('m')
	/* 默认控制器 */
	->setDefaultController('Welcome')
	/* 默认方法 */
	->setDefaultMethod('index')
	/* 控制器方法前缀 */
	->setMethodPrefix('do_')
	/* 方法url后缀 */
	->setMethodUriSubfix('.do')
	/* 注册hmvc模块，数组键是uri里面的hmvc模块名称，值是hmvc模块文件夹名称 */
	->setHmvcModules(array(
		// 'Demo' => 'demo'
	))
	/* hvmc模块域名绑定
	 * 1.子域名绑定
	 * domains的键是二级开始的域，不包含顶级域名.
	 * 比如顶级域名是test.com,这里的domains的键是demo代表demo.test.com
	 * 再比如domains的键是i.user代表i.user.test.com
	 * isFullDomain这里设置为false.
	 * 2.完整域名绑定
	 * domains的键是完整的域名,比如demo.com,
	 * isFullDomain这里设置为true.
	 * 配置项介绍:
	 * 	0.最外层的enable是总开关.
	 * 	1.hmvcModuleName是域名要绑定的hmvc模块名称，
	 * 	   也就是对应着上面的setHmvcModules()注册的关联数组中的"键"名称.
	 * 	2.demo下面的enable是单个域名绑定是否启用.
	 * 	3.domainOnly是否只能通过绑定的域名访问hvmc模块.
	 * 	4.绑定完整的域名isFullDomain这里设置为true.
	 * 	   绑定子域名isFullDomain这里设置为false.
	 */
	->setHmvcDomains(array(
	    'enable' => false, //总开关，是否启用
	    'domains' => array(
		'demo' => array(
		    'hmvcModuleName' => 'Demo', //hvmc模块名称
		    'enable' => false, //单个开关，是否启用
		    'domainOnly' => true, //是否只能通过绑定的域名访问
		    'isFullDomain' => false//绑定完整的域名设置为true；子域名设置为false
		)
	    )
	))
	/* 设置session信息 */
	->setSessionConfig(array(
	    'autostart' => false,
	    'cookie_path' => '/',
	    'cookie_domain' => \Sr::server('HTTP_HOST'),
	    'session_name' => 'SOTER',
	    'lifetime' => 3600,
	    'session_save_path' => null, //Sr::config()->getStorageDirPath().'/sessions'
	))
	/* 设置cookie key前缀，当我们使用Sr::setCookie()的时候，
	 * 参数里面的key自动加上这里设置的前缀 */
	->setCookiePrefix('')
	/* 设置加密方法Sr::encrypt()和解密方法Sr::decrypt()使用的密钥,
	 * 只有这里设置了密钥，当不传递key的时候，这两个方法才能正常使用。
	 * 提示：这里可以使用数组指定三个环境下的密钥，还可以传递一个字符串
	 * 这个字符串就是所有环境使用的密钥。 */
	->setEncryptKey(array(
	    'development' => '', //开发环境密钥
	    'testing' => '', //测试环境密钥
	    'production' => ''//产品环境密钥
	))
	/**
	 * 配置缓存
	 * 1.setCacheHandle可以直接传入缓存配置数组。
	 * 2.setCacheHandle也可以传入配置文件名称，配置文件里面要返回一个缓存配置数组。
	 * 缓存配置数组可以参考缓存配置文件：application/config/default/cache.php里面return的数组。
	 * 3.如果这里不设置(保留注释)，Sr::cache()默认使用的是文件缓存，
	 * 缓存数据默认存储在application/storage/cache
	 */
	//->setCacheConfig('cache')
	/* 设置数据库连接信息，参数可以是配置文件名称；也可以是数据库配置信息数组，即配置文件返回的那个数组。 */
	//->setDatabseConfig('database')
	/* 设置自定义的错误显示控制处理类 */
	->setExceptionHandle(new \Soter_Exception_Handle_Default())
	/* 错误日志记录，注释掉这行会关闭日志记录，去掉注释则开启日志文件记录,
	 * 第一个参数是日志文件路径，第二个参数为是否记录404类型异常 */
	//->addLoggerWriter(new \Soter_Logger_FileWriter(\Sr::config()->getStorageDirPath() . 'logs/', false))
	/* 设置日志记录子目录格式，参数就是date()函数的第一个参数,默认是 Y-m-d/H */
	->setLogsSubDirNameFormat('Y-m-d/H')
	/*
	 * 拓展Sr类的方法，参数是关联数组，键是拓展的方法名称，值是前缀字符串或者回调匿名函数
	 * 详细使用请看手册：拓展Sr核心
	 */
	->setSrMethods(array())
	/**
	 * 设置session托管类型
	 * 1.setSessionHandle可以直接传入Soter_Session类对象
	 * 2.setSessionHandle也可以传入配置文件名称，配置文件里面要返回一个Soter_Session类对象。
	 */
	//->setSessionHandle('session')
	/* 设置控制器方法缓存规则，参数可以是配置文件名称，也可以是配置规则数组 */
	//->setMethodCacheConfig('method_cache')
	/* 设置自定义数据验证规则，参数可以是配置文件名称，也可以是规则数组 */
	//->setDataCheckRules('rules')
	/* 设置Sr::json()输出处理回调函数，这里可以自定义json输出格式 */
	->setOutputJsonRender(function() {
		$args = func_get_args();
		$code = \Sr::arrayGet($args, 0, '');
		$message = \Sr::arrayGet($args, 1, '');
		$data = \Sr::arrayGet($args, 2, '');
		return @json_encode(array('code' => $code, 'message' => $message, 'data' => $data));
	})
	/* 设置发生异常的时候，调用异常对象的renderJson()方法输出json的回调函数，这里可以自定义json输出格式 */
	->setExceptionJsonRender(function(Exception $e) {
		$json['environment'] = $e->getEnvironment();
		$json['file'] = $e->getErrorFile();
		$json['line'] = $e->getErrorLine();
		$json['message'] = $e->getErrorMessage();
		$json['type'] = $e->getErrorType();
		$json['code'] = $e->getErrorCode();
		$json['time'] = date('Y/m/d H:i:s T');
		$json['trace'] = $e->getTraceCliString();
		return @json_encode($json);
	})
	
	
;

//启动，噪起来
\Soter::run();

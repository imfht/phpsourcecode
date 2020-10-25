---
layout: post
title: 掌握实用php技巧事半功倍进行PHP开发
description: 网上有不少关于PHP使用技巧介绍的相关文章，一条条的被奉为圣经，比如说循环遍历推荐采用foreach而不是for，不建议采用引用等等，但却鲜有人详细介绍为什么要这么使用。基于此，打算对从PHP源码和原理的角度分析PHP使用技巧。一方面是对自己知识的积累和梳理，另外一方面也希望能够分享给更多的朋友
keywords: php技巧, php开发
author: 网络技术test
category: [网络技术]
tags: [php, 技巧, 编程]
---
@hi@ 中的 @php@ 系列文章主要服务于PHP使用者(能够更好的理解 **PHP使用技巧**，并且举一反三，触类旁通)和PHP研究者（希望深入研究PHP源码的朋友，希望能够成为PHP源码入门的引导并 **掌握实用php技巧事半功倍进行PHP开发**。

## php技巧-定义一个ROOT路径而不要总是用相对路径
下面的代码很常见：

	require_once('../../lib/some_class.php');

这个方法在 **php开发** 中有诸多缺陷：

*   它会先在PHP的include路径中查找，接着在当前目录中查找，因此会检查许多目录。
*   如果脚本由其他目录中的脚本所引用，目录的调整会引发问题。
*   当以计划任务运行脚本时，以相对路径形式可能无法找到父目录

比较好的 **php技巧**是采用绝对路径进行 **php开发**：

	define('ROOT' , '/var/www/project/');
	require_once(ROOT . '../../lib/some_class.php');

当然， 这里有绝对路径和常量。可以再来改进一下。考虑使用魔术常量，比如 `__FILE__`,看看这样如何：

	define('ROOT' , pathinfo(__FILE__, PATHINFO_DIRNAME));
	require_once(ROOT . '../../lib/some_class.php');

好了，现在可以迁移你的项目到不同的目录了，比如迁移到在线服务器，无需做任何改动。

## php helper函数技巧而不使用require进行php开发

很多朋友在 **php开发** 代码顶部会引用很多，比如类库、文件、小工具以及其他helper函数，在**php开发**过程中使用了很多require, include, require_once, include_once 等， 比如这样：

	require_once('lib/Database.php');
	require_once('lib/Mail.php');
	require_once('helpers/utitlity_functions.php');

这样有些原始了。代码需要有弹性。动手写个能更容易引用的helper函数吧。看这个例子：

	function load_class($class_name)
	{
		//path to the class file
		$path = ROOT . '/lib/' . $class_name . '.php');

		if(file_exists($path))
		{
			require_once( $path );
		}
	}

它可以完成以下工作：

*   在多目录中搜索相同的类文件
*   当改变引用库目录时会非常容易，而不用到处去修改代码
*   如果需要引用html内容，稍加修改就成了load-htm

## php开发技巧之建立应用程序中的调试环境

php开发过程中遇到问题时，我们可能会输出DB查询、dump变量……，问题解决后我们会注释掉或者删除。其实一个很有用的php技巧是应该留着它们。

	define('ENVIRONMENT' , 'development');
	if(! $db-&gt;query( $query )
	{
		if(ENVIRONMENT == 'development')
		{
			echo "$query failed";
		}
		else
		{
			echo "Database error. Please contact administrator";
		}
	}

## 用Session传送状态消息

在完成一些任务后，系统/应用程序会进行一些消息提示

	&lt;?php
	if($wrong_username || $wrong_password)
	{
		$msg = 'Invalid username or password';
	}
	?&gt;
	&lt;html&gt;
	&lt;body&gt;
	&lt;?php echo $msg; ?&gt;
	&lt;form&gt;
	...
	&lt;/form&gt;

这些代码很常见。但这种方法存在局限性：

*   不能传递跳转地址（打算用GET参数传递？亲，该吃药……）
*   消息过多时管理困难

最好的办法是用Session传递，当然，请记得session_start。

	function set_flash($msg)
	{
		$_SESSION['message'] = $msg;
	}

	function get_flash()
	{
		$msg = $_SESSION['message'];
		unset($_SESSION['message']);
		return $msg;
	}
	&lt;?php
	if($wrong_username || $wrong_password)
	{
		set_flash('Invalid username or password');
	}
	?&gt;
	&lt;html&gt;
	&lt;body&gt;
	Status is : &lt;?php echo get_flash(); ?&gt;
	&lt;form&gt;
	...
	&lt;/form&gt;
	&lt;/body&gt;
	&lt;/html&gt;

## 弹性化你的函数

	function add_to_cart($item_id , $qty)
	{
		$_SESSION['cart'][$item_id] = $qty;
	}
	add_to_cart( 'IPHONE3' , 2 );

用上面的函数可以添加一个商品。如果需要添加多件，我们又要新建一个函数么？NO。 只需“弹性化”即可，看这个：

	function add_to_cart($item_id , $qty)
	{
		if(!is_array($item_id))
		{
			$_SESSION['cart'][$item_id] = $qty;
		}
		else
		{
			foreach($item_id as $i_id =&gt; $qty)
			{
				$_SESSION['cart'][$i_id] = $qty;
			}
		}
	}
	add_to_cart( 'IPHONE3' , 2 );
	add_to_cart( array('IPHONE3' =&gt; 2 , 'IPAD' =&gt; 5) );

现在，一个函数可以接受多种类型，此方法在很多地方都可应用。

## 忽略php的收尾标记

当收尾标记**?&gt;**之后有额外的字符（比如空格），你此刻需要echo 一个image或pdf，或者玩cookies/sessions， 你会看到”headers already send” error。原因在于额外的字符被显示出来了，你可能需要浪费数小时去寻找这些“额外字符”。

避免此问题的方法就是，请忽略收尾标记**?&gt;**，好多了吧？

## 收集所有输出， 再一次输出给浏览器

这玩意是输出缓冲。比如你需要用多个函数输出内容：

	function print_header()
	{
		echo "&lt;div id='header'&gt;Site Log and Login links&lt;/div&gt;";
	}
	function print_footer()
	{
		echo "&lt;div id='footer'&gt;Site was made by me&lt;/div&gt;";
	}
	print_header();
	for($i = 0 ; $i &lt; 100; $i++)
	{
		echo "I is : $i &lt;br /&gt;';
	}
	print_footer();

考虑这么做：首先收集所有输出到一个地方。可以存在变量里，也可以用ob_start/ob_end_clean. 改改看：

	function print_header()
	{
		$o = "&lt;div id='header'&gt;Site Log and Login links&lt;/div&gt;";
		return $o;
	}
	function print_footer()
	{
		$o = "&lt;div id='footer'&gt;Site was made by me&lt;/div&gt;";
		return $o;
	}
	echo print_header();
	for($i = 0 ; $i &lt; 100; $i++)
	{
		echo "I is : $i &lt;br /&gt;';
	}
	echo print_footer();

为何需要输出缓冲？

*   发送给浏览器之前可以改动输出。 比如文本/正则替换，或者加一些额外的html代码，比如 profiler/debugger
*   同时进行php处理与输出是个坏习惯。

## 输出非html内容时，通过header发送正确的mime类型

xml：

	header("content-type: text/xml");
	echo $xml;

Javascript:

	header("content-type: application/x-javascript");
	echo "var a = 10";

CSS:

	header("content-type: text/css");
	echo "#div id { background:#000; }";

## mysql连接时设置正确的字符编码

如果mysql表以unicode/utf-8正确存储，phpmyadmin中也可正确显示，但读取数据显示在页面时乱码出现，问题则出在mysql连接整理上：

	$c = mysqli_connect($host , $username, $password);
	mysqli_set_charset ( $c , 'UTF8' );

当连接到数据库时，设置整理字符集是一个好习惯，在开发多语言的项目中尤为重要。

## 使用htmlentitis设置正确的字符集选项

PHP 5.4之前的默认字符编码是ISO-8859-1，无法显示诸如À â等字符。

	$value = htmlentities($this-&gt;value , ENT_QUOTES , 'UTF-8');

PHP 5.4起，默认编码是UTF-8，这将解决大部分问题。如果你的应用程序为多语种，请注意这里


## 不要使用gzip输出，让apache去做这个

考虑用ob_gzhandler? 别这么做，这样没什么意义。不要担心在php上如何优化服务器和浏览器之间的数据传输。在Apache中启用mod_gzip或者mod_deflate来压缩吧。


## 使用json_encode 在PHP中打印javascript代码

有时需要在PHP中动态生成一些javascript代码：

	foreach($images as $image)
	{
		$js_code .= "'$image' ,";
	}
	$js_code = 'var images = [' . $js_code . ']; ';
	echo $js_code;
	//Output is var images = ['myself.png' ,'friends.png' ,'colleagues.png' ,];

试试json_encode吧：

	$images = array(
	'myself.png' , 'friends.png' , 'colleagues.png'
	);
	$js_code = 'var images = ' . json_encode($images);
	echo $js_code;
	//Output is : var images = ["myself.png","friends.png","colleagues.png"]

## 在写文件之前请先检查目录是否可写入

写入任何文件之前，请确认该文件所在目录是否可写，如不可写，提示错误信息。这会帮你节省无数“调试”时间。当你在linux下干活时，目录不能被写入、不能读取文件时要首先考虑目录权限问题。

确保你的程序在最短时间内，可以尽量智能化地报告出最重要的错误信息。

	$contents = "All the content";
	$file_path = "/var/www/project/content.txt";
	file_put_contents($file_path , $contents);

代码没问题， 但可能会有些间接问题产生。File_put_contents失败的可能原因如下：

*   父目录不存在
*   目录存在，但不可写
*   文件被锁定

因此，最好在写入文件之前先进行检测。

	$contents = "All the content";
	$dir = '/var/www/project';
	$file_path = $dir . "/content.txt";
	if(is_writable($dir))
	{
		file_put_contents($file_path , $contents);
	}
	else
	{
		die("Directory $dir is not writable, or does not exist. Please check");
	}

这样做的话，当文件写入失败时你会知道准确的信息。

## 更改您的应用程序创建的文件权限

当你在linux环境下工作时，权限处理会浪费很多时间。因此，当你的应用程序创建文件后，进行chmod以确保外部可以访问。否则会带来很多麻烦。例如，生成的文件由“PHP”用户所创建，而您开发时是另一个用户，系统会禁止您访问或打开文件，之后你可能需要取得root权限再变更文件权限……

	// Read and write for owner, read for everybody else
	chmod("/somedir/somefile", 0644);
	// Everything for owner, read and execute for others
	chmod("/somedir/somefile", 0755);

## 不要通过检查提交按钮的值来判断表单提交

	if($_POST['submit'] == 'Save')
	{
		//Save the things
	}

上面的代码看起来的确没什么错。但，当你的程序是多语言时，就不一定叫Save了，这时怎么判断？所以，不要依赖提交按钮的值了，这么做吧：

	if( $_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['submit']) )
	{
		//Save the things
	}

## 考虑在函数中使用静态变量

	//Delay for some time
	function delay()
	{
		$sync_delay = get_option('sync_delay');
		echo "&lt;br /&gt;Delaying for $sync_delay seconds...";
		sleep($sync_delay);
		echo "Done &lt;br /&gt;";
	}

使用静态变量之后：

	//Delay for some time
	function delay()
	{
		static $sync_delay = null;
		if($sync_delay == null)
		{
			$sync_delay = get_option('sync_delay');
		}
		echo "&lt;br /&gt;Delaying for $sync_delay seconds...";
		sleep($sync_delay);
		echo "Done &lt;br /&gt;";
	}

## 不要直接使用$_SESSION变量

	$_SESSION['username'] = $username;
	$username = $_SESSION['username'];

熟悉吧？但这么做有问题。

如果在相同域下运行多个程序，session变量可能会冲突， 2个不同的应用程序可能设置了相同key的session变量。

因此，用wrapper函数指定一下key吧：

	define('APP_ID' , 'cichui.com');
	//Function to get a session variable
	function session_get($key)
	{
		$k = APP_ID . '.' . $key;
		if(isset($_SESSION[$k]))
		{
			return $_SESSION[$k];
		}
		return false;
	}
	//Function set the session variable
	function session_set($key , $value)
	{
		$k = APP_ID . '.' . $key;
		$_SESSION[$k] = $value;
		return true;
	}

## 18. 将辅助函数(utility helper functions)封装成一个类

你可能有很多像这样的辅助函数：

	function utility_a()
	{
	//This function does a utility thing like string processing
	}
	function utility_b()
	{
	//This function does nother utility thing like database processing
	}
	function utility_c()
	{
	//This function is ...
	}

你可以考虑把他们封装成类的静态方法：

	class Utility
	{
		public static function utility_a()
		{
		}
		public static function utility_b()
		{
		}
		public static function utility_c()
		{
		}
	}
	//and call them as
	$a = Utility::utility_a();
	$b = Utility::utility_b();

这有一个明显的好处是，不会和PHP自带函数命名冲突。另一个角度看，你可以在同一个应用程序内建立多个版本，不会有任何冲突。只是最基本的封装， 没别的。

## 一些愚蠢的小技巧

*   用echo代替print
*   除非绝对必要，请用str_replace代替preg_replace
*   不要使用短标记(&lt;?=)[瓷锤注：PHP 5.4已经默认开启短标记了^_^]
*   简单字符串使用单引号
*   永远记得在header跳转后exit
*   永远不要在for循环控制行里调用函数
*   isset比strlen快
*   在循环或if-else代码块中请坚持使用大括号{} （即使一行）。不要尝试通过“吃掉语法”而让你的代码变短，请让你的逻辑更短一些。
*   使用语法高亮的编辑器，代码高亮有助于帮你减少错误

## 使用array_map快速处理数组

想清理(trim)一个数组中的所有元素？新手一般会这样：

	foreach($arr as $c =&gt; $v)
	{
		$arr[$c] = trim($v);
	}

更清爽的做法是：

	$arr = array_map('trim' , $arr);

此函数会将trim应用于所有$arr数组中的元素。另一个类似的函数是array_walk，具体请参见PHP帮助文档。

## 使用PHP filters扩展验证数据

你用正则做过数据校验吧？比如email, ip地址等等……是得，每个人都做过这些。 现在试试这个——PHP的[filters](http://cn.php.net/filter)扩展。

	if (filter_var($email_a, FILTER_VALIDATE_EMAIL)) {
	//…
	}
	if (filter_var($ip_a, FILTER_VALIDATE_IP)) {
	//…
	}

除此以外，还有：FILTER_VALIDATE_URL，FILTER_VALIDATE_REGEXP………

## 强制类型转换

	$amount = intval( $_GET['amount'] );
	$rate = (int) $_GET['rate'];

强类型转换是个好习惯。

## 使用set_error_handler() 将PHP错误日志写入文件

set_error_handler()可以用来设置自定义错误。用它把错误日志写入日志文件也是个不错的主意。

## 小心处理大数组

如果一个变量存有大型数组或者字符串，请小心处理。通常的错误是创建副本然后内存耗尽，得到一个内存超出的致命错误。

	$db_records_in_array_format; //1000行＊20列，每行至少100 字节 , so total 1000 * 20 * 100 = 2MB
	$cc = $db_records_in_array_format; //用掉2MB
	some_function($cc); //擦，还要再用2MB ?

上面的代码是普通的CSV文件导入（或导出）。这么干脚本可能会超出内存限值。小规模的当然没有问题，大数组时还是要提防的。

考虑引用传参(by reference)吧， 或者存储到类变量里。

	$a = get_large_array();
	pass_to_function(&amp;$a);
	class A
	{
		function first()
		{
			$this-&gt;a = get_large_array();
			$this-&gt;pass_to_function();
		}
		function pass_to_function()
		{
		//process $this-&gt;a
		}
	}

大数组变量用毕记得尽快注销掉(unset)。

## 整个脚本中使用一个数据库连接

连接数据库时，请确保您使用一个连接。开始打开连接并开始使用，直到结束，并在结束时关闭连接。

请不要这么做：

	function add_to_cart()
	{
		$db = new Database();
		$db-&gt;query("INSERT INTO cart .....");
	}
	function empty_cart()
	{
		$db = new Database();
		$db-&gt;query("DELETE FROM cart .....");
	}

多次数据库连接很糟糕，由于每次连接都需要消耗时间和更多内存，它们会让执行时间变得更慢。 可以考虑使用单件模式(Singleton pattern)进行数据库连接。


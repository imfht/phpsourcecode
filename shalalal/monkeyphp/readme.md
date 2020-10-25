##MonkeyPHP

MonkeyPHP是一个完全面向对象的轻量级 PHP 框架！ 

###主要特点：
* 一、设计规范，容易部署。
* 二、支持 MVC 和 REST 等web架构。 
* 三、路由既高效强大，又易于定制。 
* 四、松耦合设计，易于扩展。 
* 五、安全的数据库操作，无 sql 注入风险。


###QQ群：
**275107751（MonkeyPHP）**；

谢谢所有围观、支持的朋友们！

##目录结构
 
>system（后端系统目录）  
>>apps（网站集后端目录）  
>>>DefaultApp（具体网站的后端总目录、DefaultApp也是这个网站的顶级命名空间）  
>>>>Controller（控制器目录）  
>>>>data（配置目录，数据目录，如路由映射表就放在这里）  
>>>>LabelApi（模板标签API目录）  
>>>>App.php（应用类）  

>>>Manual（框架手册网站的后端总目录）  

>>vendor（组件集目录）  
>>>composer（自动加载组件）  
>>>markdown（php markdown lib 组件）  
>>>monkey（MonkeyPHP框架组件）  

> www（前端虚拟空间目录） 
  
>>defaultForeground（某网站的前端目录）  
>>>mySkinName（皮肤样式目录）  
>>>public（公共静态资源目录）  
>>>.htaccess  
>>>index.php（入口文件）  

>>.htaccess  
>>index.php（入口文件）  
   
   
defaultForeground目录名是随便起的，里面的入口文件和外面的入口文件差不多：  
前端目录内的入口文件 defaultForeground/index.php  

	//启动自动加载   
	require(__DIR__.'/../../system/vendor/autoload.php');    
	//建立应用,参数1：应用命名空间；参数2：前端目录。    
	$app= new DefaultApp\App(__DIR__);
	//运行应用  
	$app->run();


前端目录外的入口文件 index.php  
	
	//启动自动加载  
	require(__DIR__.'/../system/vendor/autoload.php');  
	//建立应用, 参数1：应用命名空间； 参数2：前端目录。  
	$app= new DefaultApp\App(__DIR__ . '/defaultForeground');
	//运行应用  
	$app->run(); 
 
##网站配置

###1. 配置的存放位置  
system/apps/网站后端目录/data/config.php。

###2. 配置写法
参考：system/vendor/monkey/Globe/data/config.default.php。

###3. 路由映射表配置
存放文件位置：system/apps/网站后端目录/data/router.map.php

	/******** 路由器到控制器的映射表——简称路由映射表（示例） ********/
	//其中请求方法get可以省略，其它如post等则不能省略
	return array(
	    //静态路由
	    'get/'       =>'Index:index',  //'Index:index'相当于请求\DefaultApp\Controller\Index类的actionIndex方法
	    '/'          =>'Index:index', //效果同上 
	    
	    'get/hello'  =>'Index:hello',  
	    '/hello'     =>'Index:hello', //效果同上 
	
	    '/blog'      =>'Blog\Blog:index', //控制器支持子命名空间
	
	    //动态标识路由，使用了路由组件配置中的编译标签
	    '/{zh|en}:language'    =>'Index:index',  //{zh|en}的匹配结果将作为参数名language的值
	    '/blog/{s}:title'      =>'Blog:get',     //{s}的匹配结果将作为参数名title的值
	    'post/article/{year}/{month}/{s}:year:month:title'    =>'Article:modify',     //这里有三个参数
	    
	    //动态正则路由，路由中直接使用正则表达式，但有个限制：不能嵌套括号！
	    '/article/([1-9]\d{3})/(1[0,1,2]|[1-9])/([^\/]+):year:month:title'=>'Article:get',
	);


##Hello World
本节我们一起来做一个输出“Hello World”的程序。

###1. 下载部署网站
下载并解压 MonkeyPHP，按照目录部署方案一设置好目录。

###2. 配置路由
找到system/apps/DefaultApp/data/router.map.php文件，在其中添加三个路由：

    '/'=>'Index:index',
    '/{zh|en}:language'=>'Index:index',
    '/hello'=>'Index:hello',

###3. 编写控制器
在system/apps/DefaultApp/Controller目录下新建一个Index.php文件：

    <?php
    namespace DefaultApp\Controller;
     
    use Monkey\Controller;
     
    /**
     * 控制器示例 Index
     */
    class Index extends Controller
	{
	    public function actionIndex()
	    {
		    $param = $this->getRouteParameter();

		    if(empty($param)){
		    	echo '--你好hello!--<br/>';
		    }
		    if($param['language']=='zh'){
		    	echo '--你好!--<br/>';
		    }
		    if($param['language']=='en'){
		    	echo '--hello!--<br/>';
		    }
		    echo date('Y-m-d H:i:s');
	    }
	     
	    public function actionHello()
	    {
	    	echo '测试hello!<br/>';
	    }
    }


###4. 在浏览器中查看你的杰作
启动你的web服务器，并在浏览器中访问如下网址：  
http://web目录/  
http://web目录/zh  
http://web目录/en  
http://web目录/hello  
######enjoy! 

##程序执行流程

###step1、接受请求
浏览器的所有请求被转移到WWW目录下的index.php文件，这个文件就是接受请求的入口文件。

###step2、启动应用
入口文件创建应用，并运行应用。

###step3、分发路由
路由器查询路由并分发给控制器

###step4、处理请求
继承Monkey\Controller的控制器处理相应的请求。

##URL 路由解析

###1. 路由解析器的构成：路由器+路由表。
这使得站长可以随时在线编辑路由表，从而更方便的控制网站。  
URL 中路由字符串的查找模式

(1) **rewrite**：http://… /www/route.html 或 http://… /www/route  
(2) **pathinfo**：http://… /www/index.php/route  
(3) **get**：http://… /www/index.php?r=route

###2. 查找原理：
路由组件先编译路由表，目的是可以让查询既高效又强大。   
路由匹配虽然使用了正则表达式，但是巧妙的编译方式使得查询匹配过程非常高效。   
注意： 
 
(1) 当选择 rewrite 查找模式时需要服务器支持，比如 apache 的.htaccess 文件（位置在 www 目录下）：  
	
		<IfModule mod_rewrite.c>  
		RewriteEngine On  
		#RewriteBase /  
		RewriteCond %{REQUEST_FILENAME} !-f  
		RewriteCond %{REQUEST_FILENAME} !-d  
		RewriteRule ^(.*)$ index.php/$1 [L]  
		</IfModule>  

(2) 当选择 pathinfo 查找模式也需要服务器支持，开启方法请百度哈。注意，IIS默认只支持pathinfo。

##控制器

控制器是 MVC 模式中的核心关键层，一个程序可以没有模型，也可以没有视图，但是控制器是必须有的。

####使用 MonkeyPHP 的控制器类需要遵以下几点。

 1. 命名空间规范
\DefaultApp\Controller\控制器路径

 2. 继承规范
必须继承 Monkey\Controller，如：

	    <?php
	    namespace DefaultApp\Controller;
	     
	    use Monkey\Controller;
	     
	    /**
	     * 控制器示例 Index
	     */
	    class Index extends Controller
		{
		    public function actionIndex()
		    {
			    $param=$this->getRouteParameter();

			    if(empty($param)){
			    	echo '--你好hello!--<br/>';
			    }
			    if($param['language']=='zh'){
			    	echo '--你好!--<br/>';
			    }
			    if($param['language']=='en'){
			    	echo '--hello!--<br/>';
			    }
			    echo date('Y-m-d H:i:s');
		    }
		     
		    public function actionHello()
		    {
		    	echo '测试hello!<br/>';
		    }
	    }

同时访问方法 必须是以 action 为前缀，如上 actionIndex 、actionHello

##视图

MonkeyPHP 的视图组件既可单独使用：$app->view(); 也可继承View使用。Widget就是继承视图组件的，你要使用Widget则只能继承Widget。

##视图中的模板标签说明
MonkeyPHP 的模板类和标签方案是分离的。 标签方案目前只提供了 \Monkey\View\Tag 方案，你可以参考并修改为其它方案，比如 dede 的、ThinkPHP 的等等。

##模型

MonkeyPHP 没有提供模型，而是提供了Drupal的数据库组件，在保持原逻辑和使用接口的前提下作了适当的精简。

##代码规范

MonkeyPHP的代码规范绝大多数是遵循PSR-0，1，2中的标准。 

##安全解决方案

###1. CSRF 跨站请求伪造解决方案：表单提交时使用 token 效验
请自己实现

###2. XSS 跨站脚本攻击解决方案：过滤浏览器输入的值
**使用方法：**
选择上节 Monkey\Library\Filter 类中的 xss####方法来过滤输入值：

(1) **xssToText($data)**，直接删除所有 html 和 php 标签，使得浏览器输入的内容只剩下文本，是最高级别的过滤；  
(2) **xssToHtml($data)**，不是删除任何标签，但对标签进行彻底的编码，使得浏览器输入的内容按源码输出，是普通级别的过滤；  
(3) **xssDeleteScript($data)**，有选择性的删除 javascript,iframes,object 等有害标签，使得其它内容能保留浏览器输入的 html 样式，是最弱的过滤级别；  

###3. SQL 防注入解决方案：drupal数据库组件
原理（详见源码）

###4. 目录遍历漏洞解决方案：不向浏览器输出后台目录变量
MonkeyPHP 的目录部署分为两个部分

(1) **www** 目录，是 web 服务器指定浏览器可以访问的虚拟目录，这里仅仅存放静态资源文件和浏览器上传的文件。同时还要通过系统设置保证这个目录里的文件没有执行权。  
(2) **system** 目录绝不能让浏览器有访问权限。  

###5. DDOS 攻击解决方案：拦截浏览器恶意刷新
公共函数中有一个有效拦截防御 DDOS 的代码。如需使用，请在 system/vendor/autoload.php 文件末尾新增一行 intercept_DDOS();

###6. 会话劫持解决方案：请使用 SSH 或 https 协议取代 http 协议

##安全工具——过滤浏览器输入类：Monkey\Library\Filter

###1. 公开方法：

**方法名：** ::compressWhite  
**参数：** array|string $data 待处理数据   
**作用：** 压缩空白字符（包括多余的换行）。返回原格式数据。

**方法名：** ::xssToText 深度防止XSS攻击代码   
**参数：** array|string $data 待处理数据   
**作用：** 删除 html 和 php 标签，使得结果只剩下文本，同时\n等换行符号也会转换。返回原格式数据。

**方法名：** ::xssToHtml 中度防止XSS攻击代码  
**参数：** array|string $data 待处理数据   
**作用：** 编码为 HTML 文本，使得浏览器输入的内容按源码输出。返回原格式数据。

**方法名：** ::xssDeleteScript 轻度防止XSS攻击代码   
**参数：** array|string $data 待处理数据   
**作用：** 删除 javascript,iframes,object等代码，保留 html 样式。返回原格式数据。

**方法名：** ::nl2br 	  
**参数：** array|string $data 待处理数据   
**作用：** 转化文本中的换行符号为<br/>。返回原格式数据。

**方法名：** ::nl2delete 	  
**参数：** array|string $data 待处理数据   
**作用：** 删除文本中的换行符号。返回原格式数据。

**方法名：** ::phptag 	  
**参数：** array|string $data 待处理数据   
**作用：** 仅仅编码 PHP 标签。返回原格式数据。

###2. 注意：

(1) ::white 主要减少浏览器输入的冗余信息量。  
(2) ::xss####则主要用来防止 xss 攻击。  
(3) 本类都是静态类，且所有方法均返回原$data 格式的数据。   


##协议
MonkeyPHP 是一个开源的 PHP 框架。发布协议使用 BSD 许可证。
版权所有 2011-2014 年由 MonkeyPHP 组织（http://www.monkeyphp.org）保留所有权利。

BSD 开源协议是一个给于使用者很大自由的协议。基本上使用者可以“为所欲为”,可以自由的使用，修改源代码，也可以将修改后的代码作为开源或者专有软件再发布。
但“为所欲为”的前提当你发布使用了 BSD 协议的代码，或则以 BSD 协议代码为基础做二次开发自己的产品时，需要满足三个条件：

>    如果再发布的产品中包含源代码，则在源代码中必须带有原来代码中的 BSD 协议。
    如果再发布的只是二进制类库/软件，则需要在类库/软件的文档和版权声明中包含原来代码中的 BSD 协议。
    不可以用开源代码的作者/机构名字和原来产品的名字做市场推广。


BSD 代码鼓励代码共享，但需要尊重代码作者的著作权。BSD 由于允许使用者修改和重新发布代码，也允许使用或在 BSD 代码上开发商业软件发布和销售，因此是对 商业集成很友好的协议。而很多的公司企业在选用开源产品的时候都首选 BSD 协议，因为可以完全控制这些第三方的代码，在必要的时候可以修改或者二次开发。 
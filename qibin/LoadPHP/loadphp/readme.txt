// +----------------------------------------------------------------------
// | Loadphp Framework designed by www.loadphp.com
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.loadphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 亓斌 <qibin0506@gmail.com>
// +----------------------------------------------------------------------

/**
 +-------------------------------------------------------
 * 更新日志
 +-------------------------------------------------------
 */
 
 
	V1.4:
		1、将数据库连接所在的方法设置为静态方法。
		2、通过新增的init($table)函数实例化数据库操作类，并设置要操作的表名。
		3、eg： $user = init("user");$user->insert(array(NULL,"loadphp"));
		4、更改首次运行判断文件的存在位置，修复多个子项目中，其他子项目不能自动创建目录及文件等bug
		5、将框架自带功能类和核心类分离(core目录和class目录)。
		6、新增commons/function和commons/class目录，优化用户自定义扩展类、函数功能。
		7、用户自定义函数在使用时无需包含，系统会自动包含。

	V1.5:
		1、主要优化数据库的操作方式
		2、支持级联操作
			实例代码：
			$user = init("user");
			$user->insert(array(NULL,"loadphp")); // 插入操作
			$user->where(array("id"=>1))->update("name"=>"loadphp"); //更新操作
			$user->where(array("id>"=>2))->del(); //删除操作
			$user->where(array("id"=>1,"name like"=>"%load%"),"or")->order("id DESC")->limit(2)->findAll(); //查询多条记录
			$user->where(array("id"=>1,"name like"=>"%load%"),"or")->order("id DESC")->limit("1,2")->findAll(array("name")); //查询多条记录
			$user->where(array("id"=>1,"name like"=>"%load%"),"or")->order("id DESC")->find(); //查询一条记录
		3、优化分页，在默认执行文件名、控制器、方法的情况下也可以添加分页

   V1.5.1:
		1、主要优化用户自定义函数加载方式。
		2、修复where、order、limit等语句参数为空时SQL语句不能正常组合的BUG。
		   现在可以这么用：
		$user=init("user");$user->where()->order()->limit()->findAll(); //查询多条记录
		3、优化init()函数，当DB对象存在时不会再次创建新的对象，只做设置表名操作。
		4、优化数据库操作类，解决用户自定义model不能完全继承父类的bug。
		5、优化数据库取得总条数的方式，用户不必查询总条数后得到，
		例如:
		以前：$user = init("user");$user->query("select * from user");echo $user->getRows(); //输出10
		现在: $user = init("user"); echo $user->getRows(); //输出10
		6、优化DB::query()方法，更好的支持select insert update delete等sql语句
		
   V1.6:
		1、优化用户配置方式，将用户配置信息从主入口文件分离，成立单独文件(loadConfig.php)
		2、新增支持URL伪静态功能。
		   打开loadphp/loadConfig.php文件，修改 IS_STATIC和STATIC_FOLLOWING的值决定是否启用伪静态(默认开启)和伪静态扩展名
		   
		   例程：
		   例如用户定义：define("IS_STATIC",true);define("STATIC_FOLLOWING",".html");
		   则以下URL方式可以接受：
		   http://servername/index.php/index.html              等同于  http://servername/index.php/index
		   http://servername/index.php/index/action.html       等同于  http://servername/index.php/index/action
		   http://servername/index.php/index/action/id/1.html  等同于  http://servername/index.php/index/action/id/1
		   
		   以上例程中的扩展名用户可以自定义，例如：
		   若：define("STATIC_FOLLOWING",".shtml");
		   则以下URL方式可以接受：
		   http://servername/index.php/index.shtml              等同于  http://servername/index.php/index
		   http://servername/index.php/index/action.shtml       等同于  http://servername/index.php/index/action
		   http://servername/index.php/index/action/id/1.shtml  等同于  http://servername/index.php/index/action/id/1
		3、访问未定义控制器或未定义动作时，会返回首页
		4、允许用户自定义模板变量界限符，在loadConfig.php文件中
		5、允许用户自定义模板文件类型
		6、优化模板文件存放方案，模板文件实则存放在view/模板套目录(默认为default)/控制器(需手工创建)下
			例如：Index控制器下面的User方法要调用的模板文件是view/default/Index/User.html
		7、允许用户修改模板套目录，在loadConfig.php文件中修改
		8、修改display使用方式，如果添加模板参数，用户不需要添加扩展名
			例如：在非Index方法中调用index.html文件(因为在Index方法中调用直接用$this->display()即可)，用法是:$this->display("index");
		9、从LoadPHP1.6开始提供两个版本的框架：core核心板和full完整版。
			核心板不提供smarty模板引擎，需用户自己将smarty的所有文件复制到loadphp/lib目录下
	
	V1.7
		1、新增访问当前控制器的方式，常量CURURL，在模板中使用<($cururl)>。
		2、优化目录创建方式。
		3、修复缓存清理机制，清理当前控制器下的缓存:$this->clearCache();清理全部缓存$this->clearAllCache();
		4、更改用户创建项目方式，所有入口文件必须与loadphp目录同级。
		例如：以前的版本可以这样:在loadphp目录同级的目录创建admin目录，在admin目录下创建index.php
		现在强制这样做：在loadphp目录同级的目录创建admin目录，在loadphp目录同级的目录创建admin.php，并define("APP_PATH",'./admin');
		5、通过第四点修改，commons目录和public目录都会创建在admin目录里，为admin项目的公用文件目录。
		6、新增pub目录与loadphp目录同级，为多个项目公用文件目录。访问方式：PUB_PATH（相对于本文件的目录）、$pub_path（相对根目录）、<($pub)>（模板中使用）。
		7、修改系统D方法名为load(系统自动调用)。
		8、优化目录自动创建。
		
	v1.8
		1、优化处理缓存URL条件
		2、重写安全检测类，使用方式：$check = new CheckInput(0); // 0为检测，安全返回true。1为屏蔽，返回屏蔽后的字符串
		3、提供切换 检测/屏蔽 方式，例如上面为检测用的，现在不用在重新创建对象，只需调用$ch->changeMeta()方法即可切换至屏蔽功能
		
	v1.9 
		1、新增$this->display("模板名@模板目录");方式访问非本控制器下的模板，例如$this->display("index@User");表示使用User控制器下的index文件
		2、修改提示消息为$this->display("notice",1/2,URL);  // 1为成功 2为失败
		3、优化模板文件，解决模板文件不能正常包含的问题
		4、新增tplpath模板变量，表示当前模板套名，用来访问不同控制器下的模板文件
		5、所有模板文件必须的Default(可自定义)目录下的一个目录中，例如header共用文件，可放在Default/public/目录下，使用时<(include file="<($tplpath)>/public/header.html")>
		6、修复DB::getRows()在有条件时不能正确得到结果的BUG，如果要查询id>5的结果数可以这样：
		$db = init("user");$db->where(array("id > "=>5))->getRows();
		
	v2.0
		1、框架基类Base.class.php改名为BaseAction.class.php
		2、模型基类DB.class.php改名为DBModel.class.php
		3、规范控制器命名规则，例如：Index控制器必须命名为IndexAction，同时继承BaseAction
		4、规范模型命名规则，例如：User模型必须命名为UserModel，同时继承DBModel
		5、优化用户扩展model，使用自定义model可调用新增函数initM()，iniM()函数和init()函数的使用方式一样，参数为要操作的表名
		6、修复提示页面有输出问题。
		7、删除Mysql.class.php类，没有必要用mysql系列函数了。
		8、说明：通过本次更新，加强了框架的MVC开发能力。
		9、下个版本或将考虑加入数据表前缀设置，精简smarty模板
		
	v2.1
		1、优化伪静态功能，用户可以自定义分隔符(默认为‘-’)，例如：index.php/index-test-name-load.html实则访问index.php/index/test/name/load
		2、优化PDO数据库操作类
		3、优化DBModel::query()方法
		4、将init()函数(实例化系统模型对象)修改为D()函数
		5、将initM()函数(实例化扩展模型)修改为M()函数

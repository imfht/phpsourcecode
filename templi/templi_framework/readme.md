#templi framework 

是一个免费开源的，快速、简单的面向对象的 轻量级PHP开发框架
-----------------------------------------
作者: 七觞酒
qq:739800600
Email:739800600@qq.com

##环境要求
* php5.4.0及以上版本。
* 可发布的web环境，apache或nginx



##目录结构

```
application
	cache
		compile
	config
	controllers
	libraries
	models
	views
templi_framework
	cache
	core
	database
	demo
	font
	helpers
	libraries
	rest
	session
	tpl
	web

```

##控制器 controller

 1.控制器的命名 nameController.php 
 2.控制器的编写

```
class adminController extends Controller{ 　　 
	//初始化函数 　　 
	public function init()
	{ 　　 
			//你的代码 　　 
	} 　　 
	//默认访问控制器 　　 
	public function index()
	{ 　　 
		// 你的代码
		//载入模型 快捷 查询 不引用模型文件 　　 
		new model(‘tableName’)->field(['id'],['name'])
		->where()->fetch(); 　　 　　 
		//载入模型文件 使用模型文件内自定义的 方法add 　　 		
		new model(‘tableName’)->add();
		$this->assign(); // 模板变量赋值 
		$this->display();//页面输出 　　 
		 
		//给模板 变量赋值 
		assign(变量名,变量值)；//每次只能给一个变量赋值 
		setOutput(数组); //批量赋值 视图输出 　　 		
		display(视图文件名,模块)； 
		//当视图文件放置在 
		// view/模块/控制器名方法名.html 文
		//件名可不写,当调用当前模块下的 视图 是 模块名可
		//不写 如$this->display(‘main’,’home’);
	}
}
```

##模型 model
 1.模型命名 nameModel.php 
 2.模型编写 　　 
```　　 
 class 表名Model extends Model
 { 　　
 	/**
 	 * 重写父方法
 	 */ 
 	function tableName()
 	{ 　　 
 		return 'tableName'; 　　     
 	} 　　 
 	//你的方法 　　 
 	public function add_news($data)
 	{ 　
 		模型方法 where() where 条件 
 		field() 查血字段 
 		fetch() 执行查询 等等 　　 
 		也可以 使用 传统方式 select（$where,$fields,.....） 　　 		update() 修改 
 		insert（） 插入 
 		getlist(); 带分页的结果 	
 		count() 统计个数 find() 查询一条记录 
 		query() sql 语句查询
 		//你的代码 　　 
 	}
}
 	 　　 
```

##视图view
``` 　　 
if语句 　　{if}{else}{if} 　　 
变量输出   {$var} 　　 
foreach 循环　　{loop $arr $r} 　　{$r[‘field’]} 　　{/loop} 　　 
使用函数 　　{date(‘Y-m-d’,$time)} 　　 
载入其他视图 　　 {template file='head' module='home'} 
同一个模块下不用填写 module 加载类库 或函数库 　　
加载模块类库或函数
```
##获取配置信息　 
获取 配置文件信息 Templi::get_config($field);

##附录


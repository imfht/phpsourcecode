<?php
/**
 * 设置常规配置
 * 模块的特殊配置项:
 * 1. {module}.template: 模板文件，相对于模板目录
 * 2. {module}.staticURI: 伪静态地址格式，关键字用花括号 {} 标识
 * 3. {module}.keys.require: 新增记录时所必须提交的字段
 * 4. {module}.keys.filter: 更新记录时将会过滤的字段(不包含主键字段), 默认对管理员无效，除非在字段前加 *
 * 5. {module}.keys.serialize: 自动序列化数据的字段，在获取数据时也自动反序列化
 * 6. {module}.keys.search: 可用于搜索的字段，在调用 mod::search() 方法时将在设置的字段中查询结果
 */
return array(
	'mod' => array( //系统设置
		'installed' => false, //是否已安装
		'language' => 'en-US', //语言
		'timezone' => 'Asia/Shanghai', //时区
		'escapeTags' => '<script><style><iframe>', //过滤上传数据中的 HTML 标签
		'pathinfoMode' => false, //如果开启, create_url() 创建的路径将包含模板入口文件
		'jsonSerialize' => true, //使用 JSON 序列化数据
		'debug' => true, //调试模式，显示错误以及修复文件。特殊值：2: 在网页中显示运行信息，3: 在浏览器控制台中显示运行信息
		'httpAuth' => false, //全局 HTTP 访问认证，如果开启，则必须登录才能访问站点；也可以将其设置为 2，将基本认证替换为摘要认证(仅系统未安装时有效，如果系统已安装，设置 2 也等于 true)
		'database' => array( //数据库设置
			'type' => 'mysql', //数据库类型
			'host' => 'localhost', //主机地址
			'name' => 'modphp', //数据库名称
			'port' => 3306, //连接端口
			'username' => 'root', //用户名
			'password' => '', //登录密码
			'prefix' => 'mod_', //数据表前缀
			),
		'session' => array( //Session 设置
			'name' => '', //名称，不设置则默认
			'maxLifeTime' => 60*24*7, //生存期(分钟)
			'savePath' => '', //保存路径，不设置则为默认
			),
		'template' => array( //模板设置
			'appPath'=>'', //应用目录
			'savePath' => 'template/', //保存目录，相对于 appPath(如果有)
			'compiler' => array(
				'enable' => false, //启用编译器
				'extraTags' => array('import', 'redirect'), //额外的 HTML 语义标签
				'savePath' => 'tmp/', //编译文件保存路径
				)
			),
		'SocketServer' => array( //Socket 服务器设置
			'port' => 8080, //监听端口
			),
		),
	'site' => array( //网站设置
		'name' => 'ModPHP', //名称
		'URL' => '', //固定 URL 地址
		'home' => array( //首页设置(相对于模板目录)
			'template' => 'index.php', //模板文件
			'staticURI' => 'page/{page}.html', //伪静态地址
			),
		'errorPage' => array( //错误页面设置(相对于模板目录)
			401 => '401.php', //401 页面
			403 => '403.php', //403 页面
			404 => '404.php', //404 页面
			500 => '500.php', //500 页面
			),
		),
	'user' => array( //用户模块设置
		'template' => 'profile.php', //模板文件
		'staticURI' => 'profile/{user_id}.html', //伪静态地址
		'keys' => array( //字段设置
			'login' => 'user_name|user_email|user_tel', //用户登录字段, 当设置为多个字段时，前台可统一使用 user 作为参数
			'require' => 'user_name|user_password|user_level', //用户注册必需字段
			'filter' => 'user_name|user_level', //用户更新过滤字段
			'serialize' => 'user_protect', //用户自序列化字段
			),
		'name' => array( //用户名设置
			'minLength' => 2, //最小长度
			'maxLength' => 30, //最大长度
			),
		'password' => array( //字段设置
			'minLength' => 5, //最小长度
			'maxLength' => 18, //最大长度
			'encryptKey' => 'MODPHP', //HTTP 摘要认证时，本地用户密码加密/解密的密钥
			),
		'level' => array( //级别设置
			'admin' => 5, //管理员
			'editor' => 4, //编辑
			),
		),
	'file' => array( //文件模块设置
		'keys' => array( //字段设置
			'require' => 'file_name|file_src', //添加数据必需字段
			'filter' => 'file_src', //更新数据过滤字段
			),
		'upload' => array( //上传设置
			'savePath' => 'upload/', //保存路径
			'acceptTypes' => 'jpg|jpeg|png|gif|bmp', //接受类型(后缀)
			'maxSize' => 1024*2, //最大体积(单位 KB)
			'imageSizes' => '64|96|128', //自动添加图像尺寸(宽度, 单位: px)
			'keepName' => false, //保留原始文件名(文件名重复则自动添加 MD5 后缀)，false 则仅使用 MD5 保存
			)
		),
	'category' => array( //分类目录模块设置
		'template' => 'category.php', //模板文件
		'staticURI' => '{category_name}/page/{page}.html', //伪静态地址
		'keys'=>array( //字段设置
			'require' => 'category_name', //添加数据必需字段
			'filter' => 'category_name', //更新数据过滤字段
			)
		),
	'post' => array( //文章模块设置
		'template' => 'single.php', //模板文件
		'staticURI' => '{category_name}/{post_id}.html', //伪静态地址
		'keys' => array( //字段设置
			'require' => 'post_title|post_content|post_time|category_id|user_id', //添加数据必需字段
			'filter' => 'post_time|user_id', //更新数据过滤字段
			'search' => 'post_title|post_content', //搜索字段
			)
		),
	'comment' => array( //评论模块设置
		'keys' => array( //字段设置
			'require' => 'comment_content|comment_time|post_id', //添加数据必需字段
			'filter' => 'comment_time|post_id|*comment_parent', //更新数据过滤字段
			)
		)
	);
#**AnmpServer 0.9.x 发布**


> AnmpServer是一款集成Apache服务器、Nginx服务器、MySQL数据库、PHP解释器的整合软件包。免去了开发人员将时间花费在繁琐的配置环境过程，从而腾出更多精力去做开发，助力PHPer学习开发。

> AnmpServer采用配置文件动态解析技术，实现了ANMP组件的绿色便携、5秒极速切换。

> 欢迎加入OSDU技术交流群，QQ群：9884956 &nbsp;&nbsp;[![加入QQ群](http://pub.idqqimg.com/wpa/images/group.png)](http://shang.qq.com/wpa/qunwpa?idkey=7393edef20eef808d5c463ba1dbb8ce6f968ba4e515cbd501ebcfe3b0434639b)



##主要特性
	1. 绿色便携免安装，可以放在U盘随身携带，轻松搭建PHP开发环境； 
	2. 精简无关文件，所有文件350MB+，可极限压缩至50MB；
	3. 所有文件均在自身目录下，未对宿主系统文件做任何修改；
	4. 所有ANMP服务支持一键启动、重启、停止；


##集成组件
	1. Apache： 2.2.x、2.4.x
	2. Nginx： 1.6.x
	3. MySQL： 5.0.96、5.1.73、5.5.x、5.6.x
	4. PHP： 5.2.17、5.3.x、5.4.x、5.5.x、5.6.x
	5. Memcahced: 1.2.6
	6. FileZilla: 0.9.41
	*. Nginx支持所有PHP版本同时运行
	*. MySQL用户名root,密码root

##监听端口
	1. Apache: 127.0.0.1:80, 0.0.0.0:8080(外网)
	2. Nginx:  127.0.0.2:80, 0.0.0.0:8081(外网)
	3. MySQL:  127.0.0.1:3306
	4. Memcahced: 127.0.0.1:11211

##附件组件
	1. ZendOptimizer/ZendGuardLoader
	2. SendMail
	

##管理工具
	1. phpMyAdmin
	2. eAccelerator
	3. memAdmin
	4. phpTz

##其他工具
	1. Notepad2
	2. XDebugClient
	3. WinCacheGrind

##部署说明
	1. 解压AnmpServer_0.9.3.7z 到 X:\Anmp(X为任意盘符)
	2. C:\Windows\System32\drivers\etc\hosts，添加域名解析
		127.0.0.1 www.anmp.net	# Apache版默认站点
		127.0.0.1 adm.anmp.net	# Apache版管理中心
		127.0.0.1 box.anmp.net  # Apache和Nginx虚拟站点

		127.0.0.2 nx.anmp.net   # Nginx版,PHP5.x.x站点
		127.0.0.2 n2.anmp.net	# Nginx版,PHP5.2.x站点
		127.0.0.2 n3.anmp.net	# Nginx版,PHP5.3.x站点
		127.0.0.2 n4.anmp.net	# Nginx版,PHP5.4.x站点
		127.0.0.2 n5.anmp.net	# Nginx版,PHP5.5.x站点
		127.0.0.2 n6.anmp.net	# Nginx版,PHP5.6.x站点


	3. 执行主程序AnmpManager.exe，通过任务栏托盘区图标管理


##截图预览
![主程序][1]
![目录列表][2]

[1]: http://static.oschina.net/uploads/space/2014/1105/003714_qx1V_156408.png
[2]: http://static.oschina.net/uploads/space/2014/0425/020610_I43j_156408.jpg

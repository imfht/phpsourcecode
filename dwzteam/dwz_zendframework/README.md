DWZ + ZendFramewrok 示例

Quick Start：

1) 下载dwz_zendframework.zip

2) 解压后，配置数据库dwz_zendframework/application/config/application.ini

3) 导入doc/dwz_zf.sql

4) 配置Apahce或其他web容器中:

	<VirtualHost *:80>
	   DocumentRoot "D:/workspace/PHP/dwz_zendframework/public"
	   ServerName localhost
	
	   # This should be omitted in the production environment
	   SetEnv APPLICATION_ENV development
	
	   <Directory />
	       Options Indexes MultiViews FollowSymLinks
	       AllowOverride All
	       Order allow,deny
	       Allow from all
	   </Directory>
	</VirtualHost>


在线demo： http://j-ui.com/

#Codeigniter 3 HMVC
___
##说明
	Codeigniter 3 对于Codeigniter 2.X系列版本进行了一次大的变革，所以很多在2.X版本的代码到3.x版本已经不适用了。
	开源代码（部分参照）CI2由Jens Segers开源的代码，向开源前辈致敬！

###使用方式
	
	git clone https://git.oschina.net/liwenlong/Codeigniter-3-HMVC.git
	cp application yourAPPPATH -r
	
	vim application/config/config.php
	
	line 20: set your base url    设置base_url
	line 21: set your directory   设置扩展目录

	line 133:$config['composer_autoload'] = true;
	line 134:$config['composer_autoload'] = APPPATH.'libraries/Composer_load.php';  composer兼容调整
	//也有利于git使用 composer产生的autoload每次都稍微有区别 让版本不好管理

###关于我
	一个二比大学出来的普通二本学生，16年6月即将毕业，求坑
	关于CI讨论，欢迎+Q 285753421
	
####lisence
	MIT
	
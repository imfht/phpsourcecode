#PHPCrazy.QQConnect

[PHPCrazy](http://git.oschina.net/Crazy-code/PHPCrazy) <br />

插件名：PHPCrazy QQ互联登录插件<br />
插件作者：Crazy <mailzhangyun@qq.com><br />
主页：http://zhangyun.org

##描述

本插件是 PHPCrazy 的QQ互联登录插件，安装之后你网站的用户可以使用QQ号码登陆你的站点

如需使用此插件请先到 http://connect.qq.com 做好准备工作，具体请查看 [QQ互联文档资料](http://wiki.connect.qq.com)

##安装过程

###修改PHPCrazy

打开文件
```
includes/controller/main/login.php
```

找到
```php
// 登录
} else {

    // 如果已登录则跳转到用户中心
	if ($GLOBALS['U']['login']) {
		header('Location: ' .HomeUrl('index.php/main:user/'));
		AppEnd();
	}

	$submit = isset($_POST['submit']) ? true : false;
	$account = isset($_POST['account']) ? $_POST['account'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';

	$continue = true;
	$error = array();
```

下一行添加
```php
$QQC = new QQConnect();
```

打开文件
```
themes/AmazeUI/login.tpl.php
```

找到
```php
<a href="#" class="am-btn am-btn-secondary am-btn-sm"><i class="am-icon-qq am-icon-sm"></i> Q Q</a>
```
替换
```php
<a href="<?php echo $QQC->Login(); ?>" class="am-btn am-btn-secondary am-btn-sm"><i class="am-icon-qq am-icon-sm"></i> Q Q</a>
```
打开文件
```
themes/AmazeUI/user.tpl.php
```
找到
```php
<li><?php echo L('注册时间'); ?>：<?php echo date('Y-m-d H:i', $GLOBALS['U']['regtime']); ?></li>
```

下一行添加
```php
    				<li>
					<?php echo L('QQC 设置'); ?>
						<?php if (empty($GLOBALS['U']['qq_openid'])): ?>
						<a href="<?php echo HomeUrl('index.php/QQConnect:bind/'); ?>"><?php echo L('QQC 绑定'); ?></a>
						<?php else: ?>
						<a href="<?php echo HomeUrl('index.php/QQConnect:unbind/'); ?>"><?php echo L('QQC 解除'); ?></a>
						<?php endif; ?>
					</li>
```

###复制文件
复制文件到 PHPCrazy 的根目录

###安装
访问 http://你的域名/index.php/QQConnect:install/

###设置
设置您的appid, appkey, scope
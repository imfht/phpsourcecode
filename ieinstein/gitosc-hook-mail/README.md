#GITOSC-HOOK-MAIL


> 在项目中为了给其他的相关人员推送开发进度，如果自己在提交完成后手动去告知，一般来说比较麻烦，借助Git Hook提交回调功能，Gitosc-hook-mail实现了在提交完成后自动发邮件给预设的观察者，这样方便相关人员及时同步信息。
*关于如何在Git@OSC上设置提交回调服务，请查阅[HOOK钩子](http://git.oschina.net/oschina/git-osc/wikis/HOOK%E9%92%A9%E5%AD%90 "HOOK钩子") *
#### 如何使用 ####
	git clone git@git.oschina.net:ieinstein/gitosc-hook-mail.git gitosc-hook-mail.git
	cd gitosc-hook-mail.git
	mv config.sample.php config.php
#### config.php 配置 ####
	return array(
    'host'=>'smtp邮件服务器的地址，例如 stmp.163.com',
    'name'=>'发件人姓名，例如 Meander River.',
    'account'=>'发件人账户，例如 example@email.com',
    'accpwd'=>'发件人账户密码',
    'authpassword'=>'Git@OSC上GIT HOOK设置的密码（为防止恶意请求）',
    'to'=>array(
        array(
            '收件人邮箱1',
            '收件人姓名1'
        ),
		array(
            '收件人邮箱2',
            '收件人姓名2'
        ),
		'收件人邮箱3'
    ),
    'cc'=>array(
        array(
            '抄送人',
            '抄送人姓名'
        	)
    	)
	);
#### 部署 ####
    将配置好的目录gitosc-hook-mail.git上传到网站服务器（LAMP或者LNMP）的根目录下即可，得到访问地址：http://exampleurl.com/gitosc-hook-mail.git/index.php
	再到Git@OSC上你的项目中，管理->PUSH钩子->开启钩子，填写上述钩子地址与密码（注意密码需与config.php文件设置相同）

#### 有任何疑问请与作者联系 ####
> E-mail:ieinstein@163.com
> QQ:1485619676(请注明Git@OSC)
	
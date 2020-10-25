#MaiSmtp.class.php

##简单介绍
smtp协议发送email，可群发，可发附件，支持CC，BC等收件方式，作者m35 <78079676@qq.com>

##使用说明（初阶）：
```php
<?php
require 'MaiSmtp.class.php';

$Mail = new MaiSmtp('smtp.126.com', 'wsskoko@126.com', 'password');
$Mail->send('标题：MaiSmtp发送测试', '<h2>内容：</h2><p>MaiSmtp.class.php powered by m35</p>', '78079676@qq.com');
```

##使用说明（进阶.1）：
```php
<?php
require 'MaiSmtp.class.php';

$Mail = new MaiSmtp('smtp.126.com:25', 'maijianhu:wsskoko@126.com', 'password');
$result = $Mail->send('标题：MaiSmtp发送测试', '<h2>内容：</h2><p>MaiSmtp.class.php powered by m35</p>', '78079676@qq.com');
if ($result)
{
    echo '发送成功';
}
else
{
    echo '<pre>';
    print_r($Mail->error);
}
```
MaiSmtp初始化参数说明：  
1、smtp.126.com：smtp邮件服务器地址，默认端口25，如果端口非25，请填写如下格式 smtp.126.com:465  
2、wsskoko@126.com：邮箱账号，如果想设定发件昵称，可填写如下格式 小买:wsskoko@126.com  
3、password：邮箱密码，如果不需要密码，可不填写  
4、调试模式：Boolean类型，填写TRUE，会记录smtp发送过程到log变量  

##使用说明（进阶.2）：
```php
<?php
require 'MaiSmtp.class.php';

$Mail = new MaiSmtp('smtp.126.com:25', 'maijianhu:wsskoko@126.com', 'password', TRUE); // 最后一个参数TRUE表示开启debug
$Mail->addRecipients('78079676@qq.com'); // 添加收件人
$Mail->addRecipients(array('zhangsan@example.com', 'lisi@example.com')); // 数组类型收件人
$Mail->addRecipients('xiaoli@example.com', 'cc'); // 抄送收件人
$Mail->addRecipients('xiaozhang@example.com', 'bc'); // 密送收件人
$Mail->changeBackup(TRUE); // 同时备份发送给自己
$result = $Mail->send('标题：MaiSmtp发送测试', '<h2>内容：</h2><p>MaiSmtp.class.php powered by m35</p>');
echo '<pre>';
if ($result)
{
    echo '发送成功';
}
else
{
    echo '错误：';
    print_r($Mail->error);
}
echo '调试过程：';
print_r($Mail->log);
```

##使用说明（更多说明）：
```php
<?php
$Mail->removeRecipients('78079676@qq.com'); // 移除收件人
$Mail->clearRecipients(); // 清空收件人

$Mail->addAttachments('./example.txt'); // 添加附件
$Mail->removeAttachments('./example.txt'); // 移除附件
$Mail->clearAttachments(); // 清空附件

// 多次发送邮件场景
$Mail->resetMail(); // 首先重置发送条件
$Mail->send('标题：MaiSmtp发送测试', '<h2>内容：</h2><p>MaiSmtp.class.php powered by m35</p>', 'other@example.com'); // 接着第二次发送
```
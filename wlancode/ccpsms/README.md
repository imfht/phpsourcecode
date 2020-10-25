**云通讯平台lumen PHPSDK**
lumen 5.3.* 安装步骤

1、命令行运行 composer require yuntongxun/sms

2、在 bootstrap/app.php 中添加如下代码：
`
$app->withFacades(true,[
    '\Yuntongxun\Facades\YuntongxunSms'=>'YuntongxunSms'
]);

$app->register(\Yuntongxun\Providers\YuntongxunSmsServiceProvider::class);

$app->configure('yuntongxunsms');
`

3、复制vendor/yuntongxun/sms/config/yuntongxunsms.php到项目config文件夹中
并设置配置信息。

###使用SDK发送模版短信
使用

发送手机短信

`YuntongxunSms::templateSMS('9635', ['param1','param2'], [138xxxxxxxx,156xxxxxxxx])`
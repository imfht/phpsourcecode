## 阿里大鱼（阿里大于）短信发送SDK(PHP简化版)
* 整个SDK就1个核心类文件约100行代码，非常轻量精简
* 兼容PHP5.2 - 7.0


### 使用示例

```php
include 'alisms.php';

$key = ''; //App Key
$secret = ''; //App Secret

//实例化类:两个参数分别为申请通过后的 App Key 和 App Secret
$alisms = new alisms($key, $secret);

//短信签名: API请求参数sms_free_sign_name的值
$alisms->sms_sign('大鱼测试'); 

//短信模板变量: API请求参数sms_param的值, 官方示例值为json格式字符串，而这里只需数组格式即可，会自动转换为json
$alisms->sms_param(array('code' => '8888', 'product' => '测试'));

//短信模板ID：API请求参数sms_template_code的值(如:SMS_12185895)
$alisms->sms_template('SMS_12185895');

//短信接收号码: API请求参数rec_num的值
$alisms->sms_mobile('13900000000');

//发送短信: 返回boolean值 TRUE 为成功 FALSE 为失败或发生异常 
$res = $alisms->send();

//如发送失败，打印错误查看对应错误代码;
if(!$res) print_r($alisms->error);
```


具体api参数作用描述请自行参考官方API alibaba.aliqin.fc.sms.num.send (短信发送)中的说明


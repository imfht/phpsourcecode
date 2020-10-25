# 工具和杂项

## 获取客户端IP地址

```php
$ip = \PhalApi\Tool::getClientIp();
```

## 生成随机字符串

```php
$len = 6;
$str = \PhalApi\Tool::createRandStr($len);

// 指定使用字符集，如6位数字验证码
$len = 6;
$str = \PhalApi\Tool::createRandStr($len, '0123456789');
```

## 数组转XML格式
```php
$arr = array('name' => 'PhalApi');
$xml = \PhalApi\Tool::arrayToXml($arr);
```

## XML格式转数组
```php
$xml = '<xml><name>PhalApi</name></xml>';
$arr = \PhalApi\Tool::xmlToArray($xml);
```

## 排除数组中不需要的键
```php
$arr = array('name' => 'PhalApi', 'age' => 18, 'url' => 'www.phalapi.net');
$newArr = \PhalApi\Tool::arrayExcludeKeys($arr, 'age,url');
// array('name' => 'PhalApi')

$arr = array(
      array('name' => 'PhalApi', 'age' => 18, 'url' => 'www.phalapi.net'),
      array('name' => 'ProApi', 'age' => 16, 'url' => 'www.proapi.cn'),
      array('name' => 'YesApi', 'age' => 15, 'url' => 'www.yesapi.cn'),
);
$newArr = \PhalApi\Tool::arrayExcludeKeys($arr, 'age,url');
// array(array('name' => 'PhalApi'), array('name' => 'ProApi'), array('name' => 'YesApi'))

```


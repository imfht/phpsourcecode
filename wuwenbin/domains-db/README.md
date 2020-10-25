域名后缀信息
=============

这是一份整理过的域名后缀信息，包括：所有顶级域名及中国(cn)，香港(hk)、澳门(mo)、台湾(tw)地区的二级域名。

另外，提供一份api接口用于查询域名后缀。

## 接口调用示例

```
<?php
include_once(dirname(__FILE__)."/api.php");
$domains = array(
    "我爱北京.中国",
    "baidu.com",
    "sina.com.cn",
);
foreach($domains as $domain) {
    // 获取域名后缀
    var_dump(get_domain_suffix($domain));
    // 获取域名punycode
    var_dump(get_domain_punycode($domain));
}
```

## 第三方库

* [Net_IDNA](https://phlymail.com/en/downloads/idna-convert.html)

## 授权协议

* [MIT](http://opensource.org/licenses/MIT)


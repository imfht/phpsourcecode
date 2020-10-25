# Welcome to Longphp [![Build Status](https://travis-ci.org/adobe/brackets.svg?branch=master)](https://www.longphp.com)
-----------------------------

### A simple php framework

[作者微博](https://weibo.com/206123787 "作者微博")

<yu@wenlong.org>

测试地址：
> 普通：http://localhost/longphp

> smarty: http://localhost/longphp/smarty

> 路由：http://localhost/aaa/bbb/ddd

```
// Nginx
location / {
    try_files $uri $uri/ /index.php?$uri&$args;
}

// 如果是二级目录
location ^/xxxx/ {
    try_files xxxx/$uri xxxx/$uri/ /xxxx/index.php?$uri&$args;
}
```
##### 详细文档地址
[文档地址](https://www.longphp.com "文档地址")

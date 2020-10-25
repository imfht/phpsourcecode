网址书签
========

__注意：本项目不新增功能，但持续维护。如使用中发现问题，请留言或者提交 issue 。谢谢！！！__

## 安装步骤

* 部署代码并配置好web服务器；
* 创建并导入`db/bookmark.sql`到数据库；
* 修改`root/config/base.php`配置数据库连接参数及其他选项；
* 申请第三方应用并配置`root/config/oauth.php`；
* 修改`root/data/*`目录为可写权限；

## url重写配置参考

配置文件`root/config/base.php`修改：

```
switch (App::getName()) {
    case "public":
        $config["rewriteRules"] = array(
            "User_Index" => "/[uid]"
        );
        break;
    case "mobile":
        $config["rewriteRules"] = array(
            "User_Index" => "/m/[uid]"
        );
        break;
}
```

nginx配置url重写规则：

```
rewrite ^/([0-9]+)$ /?do=User_Index&uid=$1&$args last;
rewrite ^/m/([0-9]+)$ /m/?do=User_Index&uid=$1&$args last;
```

apache配置`.htaccess`url重写规则：
```
<ifmodule mod_rewrite.c>
RewriteEngine On
RewriteRule ^([0-9]+)$ /?do=User_Index&uid=$1&%{QUERY_STRING} [L]
RewriteRule ^m/([0-9]+)$ /m/?do=User_Index&uid=$1&%{QUERY_STRING} [L]
</ifmodule>
```

## 第三方应用申请地址

* 微博：<http://open.weibo.com/connect>
* QQ：<http://connect.qq.com/>
* 百度：<http://developer.baidu.com/ms/oauth>

## 新浪SAE配置说明

* 数据库表请使用SAE提供的`PHPMyAdmin`导入；
* 数据库连接配置请直接填写`SAE_MYSQL_*`开头的常量；
* 鉴于SAE目录不能直接写入文件，所以模板编译路径需要配置到临时目录`SAE_TMP_PATH`，示例如下：

配置文件`root/config/base.php`添加如下代码：

```
// 模板编译路径
$config["templateCompilePath"] = SAE_TMP_PATH;
return $config; // 添加在此行之前
```

## 阿里云ACE配置说明

* 创建应用，并开通数据库扩展服务，使用mysql客户端工具导入数据库表；
* 数据库连接配置参考如上云数据库配置；
* URL重写参考阿里云ACE文档，如下给出一个示例配置：

```
// app.yaml添加如下代码：
rewrite:
    - url: ^/([0-9]+)$
      script: /index.php?do=User_Index&uid=$1&$args last
    - url: ^/m/([0-9]+)$
      script: /m/index.php?do=User_Index&uid=$1&$args last
```

## API接口文档

* [API.md](API.md)

## 官方主页

* <http://f.wuwenbin.info>

## 授权协议

* MIT


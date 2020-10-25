[[English Readme](README.md)]  [[中文文档](README_CN.md)]

# 关于
Tiny Wiki 是一个极简的在线文档中心, 它可以运行在现今流行的服务器环境上, 例如 apache+php 或 nginx+php

Under the [MIT License](LICENSE.md)

# 作者
+ xiaozhuai [xiaozhuai7@gmail.com](xiaozhuai7@gmail.com)

# 指南

## 配置
默认的配置文件在 `framework/config.default.json` , 如果需要修改配置, 只需要在项目目录下建立 `config.custom.json` 文件, 所有的配置项都可以被覆盖

#### ***books***

如果是一个字符串, 则是书籍的目录(相对于项目目录), 例如 `/samples/sample1`。 也可以提供一个数组, 来配置多本书籍, 例如:
```
[
    {
        "path": "/samples/sample1",
        "uri": "/"
    },
    {
        "path": "/samples/sample2",
        "uri": "/sample2"
    }
]
```
如果提供的是一个字符串 `/samples/sample1`, 那么它的等价配置是:
```
[
    {
        "path": "/samples/sample1",
        "uri": "/"
    }
]
```

##### ***小技巧***
 
得益于多模块文档的功能，在TinyWiki中很容易实现某本书的多语言，创建一个config.custom.json，配置books项如下：
```
[
    {
        "path": "/books/xx_sdk_wiki_cn",
        "uri": "/xx_sdk_wiki"
    },
    {
        "path": "/books/xx_sdk_wiki_cn",
        "uri": "/xx_sdk_wiki_cn"
    },
    {
        "path": "/books/xx_sdk_wiki_en",
        "uri": "/xx_sdk_wiki_en"
    }
    {
        "path": "/books/xx_sdk_wiki_jp",
        "uri": "/xx_sdk_wiki_jp"
    },
    {
        "path": "/books/xx_sdk_wiki_kr",
        "uri": "/xx_sdk_wiki_kr"
    }
]
```
然后便可以通过xx_sdk_wiki_${region}来访问对应的语言版本，是的，没错，我们还可以设置默认的语言为cn，即访问xx_sdk_wiki时会访问到中文的版本：
```
{
    "path": "/books/xx_sdk_wiki_cn",
    "uri": "/xx_sdk_wiki"
}
```

#### ***site_root***

站点目录, 例如，如果你将项目至于 `/var/www/wiki` 目录下, 你需要将其设置为 `/wiki`. 如果在 `/var/www`, 使用默认值 `/` 即可

#### ***theme***

主题目录, 你可以自己开发主题, 但需要注意的是, 主题至少包含 `view/layout.php` 和 `view/login.php` 模板文件，当然, 你可以自己写模板文件


## 书籍设定

### book.json

#### ***theme***

覆盖全局设定中设置的主题，意味着你可以为不同的书使用不同的主题

#### ***title***

设置书名

#### ***password***

设置密码, 可以不设或为空, 即不需要密码

#### ***duoshuo***

设置多说标识, [duoshuo](http://duoshuo.com/) 是一个社会化评论插件， 如果你想关闭此功能，将此项设为空或不设即可

#### ***menu***

设置目录结构

### 404.md
设置自定义404页

## 关于路由

例如 /xxx, 会依次匹配下面的规则, 直到命中

1. xxx.md

2. xxx/index.md

3. 404.md

4. 默认404内容, 内容为:
```
# 404
404 Not Found
```

# 关于示例书籍
示例书籍来源于 [leetcode-solution](https://github.com/siddontang/leetcode-solution), 作者信息:
+ 陈心宇 [collectchen@gmail.com](collectchen@gmail.com)
+ 张晓翀 [xczhang07@gmail.com](xczhang07@gmail.com)
+ SiddonTang [siddontang@gmail.com](siddontang@gmail.com)

感谢！

> 在这个项目中, 我加入了两个示例书籍, 便于大家配置多模块文档时参考, 具体可参考前文
>
> 示例1： [http://115.159.31.66/tiny_wiki/](http://115.159.31.66/tiny_wiki/)
>
> 示例2： [http://115.159.31.66/tiny_wiki/sample2/](http://115.159.31.66/tiny_wiki/sample2/)

# By The Way

重定向规则是必须的，所有请求(除静态文件)以外，都应该被重定向到index.php，apache下的.htaccess配置如下：
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    #ignore if it's a file
    RewriteCond %{REQUEST_FILENAME} !-f
    #redirect all request to index.php
    RewriteRule .* index.php
</IfModule>
```
你也可以很轻松的在google上找到在 nginx, lighthttpd 或其他服务器前端的等价配置 :)

# 最后
找一个前端小伙伴，由于本人前端能力有限，求小伙伴加入。

此项目纯属个人闲暇时间的作品，目标是极简，灵活，高度可配置。

联系QQ: 798047000
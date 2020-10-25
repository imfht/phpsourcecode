[[English Readme](README.md)]  [[中文文档](README_CN.md)]

# About
Tiny Wiki is a tiny document center, it can ru with most of current web env, such as apache+php and nginx+php

Under the [MIT License](LICENSE.md)

# Author
+ xiaozhuai [xiaozhuai7@gmail.com](xiaozhuai7@gmail.com)

# Guide

## Config
Default config file locate in `framework/config.default.json` , if you want to change it, just create a new file named `config.custom.json` in the project root dir, any thing can be override

#### ***books*** 

If it's a string, it's the path of book, for example `/samples/sample1`. Also you can provide an array, like:
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
If you provide a string `/samples/sample1`, it's the same with:
```
[
    {
        "path": "/samples/sample1",
        "uri": "/"
    }
]
```

##### ***Tricks***
 
It's easy to add Multi-Language Support on TinyWiki, let's try. Generate a config.custom.json, and give the books an array like :
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
Yes, you can guess what I will do next ~~
I can visit an English version by xx_sdk_wiki_en，a Chinese version by xx_sdk_wiki_cn ...
Even the default version set to Chinese, so you can visit xx_sdk_wiki for default Chinese version.
All option is up to you. just config it like this:
```
{
    "path": "/books/xx_sdk_wiki_cn",
    "uri": "/xx_sdk_wiki"
}
```

#### ***site_root***

Site root dir, it means if you put the project in  `/var/www/wiki`, you shoud give it `/wiki`. If in `/var/www`, it's `/` by default

#### ***theme*** 

Theme dir, you can develop your themes, at least, it should contains `view/layout.php` and `view/login.php` model file


## Book Config

### book.json

#### ***theme*** 

To override theme config in global config in default or custom config, you can set defferent themes for defferent books

#### ***title*** 

Define the book title

#### ***password*** 

Define the book password, can be empty

#### ***duoshuo*** 

Define the duoshuo comment shortname, [duoshuo](http://duoshuo.com/) is a social comment plugin, just leave it empty if you donnot want to enable this plugin

#### ***menu*** 

Define the book menu struct

### 404.md
Set a custom 404 page

## About Route

for example /xxx, it will match these case until matched

1. xxx.md

2. xxx/index.md

3. 404.md

4. default 404 content, the content is:
```
# 404
404 Not Found
```

# About Sample Book
The sample book is from [leetcode-solution](https://github.com/siddontang/leetcode-solution), author:
+ 陈心宇 [collectchen@gmail.com](collectchen@gmail.com)
+ 张晓翀 [xczhang07@gmail.com](xczhang07@gmail.com)
+ SiddonTang [siddontang@gmail.com](siddontang@gmail.com)

Thanks！

> In this project, I put two books as I said
>
> for sample1, go [http://115.159.31.66/tiny_wiki/](http://115.159.31.66/tiny_wiki/)
>
> for sample2, go [http://115.159.31.66/tiny_wiki/sample2/](http://115.159.31.66/tiny_wiki/sample2/)

# By The Way

Redirect rules is necessary. An Apache .htaccess file like:
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    #ignore if it's a file
    RewriteCond %{REQUEST_FILENAME} !-f
    #redirect all request to index.php
    RewriteRule .* index.php
</IfModule>
```
You will simply find the rules on nginx, lighthttpd or others by just google :)

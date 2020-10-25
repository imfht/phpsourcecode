# Leonly wiki
个人wiki站，基于wikitten并兼容mdwiki。[兼容方案参考自](http://www.tuicool.com/articles/ZRfyequ)

## 目录构造
```
config.json             mdwiki配置文件
config.php              wikitten配置文件
generate-index.sh       自动生成每个目录下的index.md(目录文件)
library                 文档目录
```

## 搭建
本站支持两种访问模式：mdwiki和wikitten，对应的入口分别是index.php和index.html。
默认启用哪个入口取决于网站的默认文件优先级。以wikitten为默认入口为例，配置方法如下：

- httpd vhost
```
DirectoryIndex index.php index.html
```
- nginx
```
index index.php index.html
```

配置后，两种访问模式访问方式：
```
// wikitten
http://yourdomain.com
// mdwiki
http://yourdomain.com/index.html
```

## wikitten改造

- 配置项：忽略空目录
config.php，开启`IGNORE_EMPTY_PATH`
```php
define('IGNORE_EMPTY_PATH', true);
```

- 忽略目录/文件
library下每一级目录可以添加ignore.php文件，配置同级目录下不需要被wikitten索引的目录或文件。

# IXCDN
## 用自建CDN加速你的网站

## 介绍
大家都知道，由于某种原因，Google API、Gravatar、WP.COM、S.W.ORG等网站在中国没办法正常访问。而且连接还是加载很久直到超时的那种没法访问，而不是一下子给你切断。这就造成了某些对这些公共字体库、AJAX库高度依赖的网站没法正常加载或者加载极端缓慢。  

IXCDN就是一个开源的反向代理程序，你只需要在你的服务器部署一下IXCDN，然后把所有谷歌和WordPress的链接指向你的CDN即可。

同时，这个程序自定义之后也可以给你自己的静态文件加速，譬如你可以在香港的服务器上部署这个程序给你美国的博客的静态文件进行缓存加速。

## 安装方法
很简单，直接用Composer即可安装（当然如果你在国内可能Composer会很慢，请百度一个Composer的国内全局镜像。
下面这三行代码请在命令提示符或者Shell下执行。
```
$ git clone https://git.oschina.net/fsgmhoward/IXCDN.git ./
$ wget https://getcomposer.org/composer.phar
$ php composer.phar update
```

## 范例
譬如[我自己的博客](https://blog.ixnet.work/)就用了这个脚本（Wordpress对谷歌和wp的公用库高度依赖，如果不用CDN的话会出现在国内无法加载/加载极端缓慢的问题。

```
fonts.googleapis.com -> cdn.ixnet.work/fonts|
ajax.googleapis.com -> cdn.ixnet.work/ajax|
fonts.gstatic.com -> cdn.ixnet.work/gs-fonts|
*.wp.com -> cdn.ixnet.work/wpcom|
*.gravatar.com -> cdn.ixnet.work/gravatar|
s.w.org -> cdn.ixnet.work/worg|
```

注意：结尾的竖线（即'|'）是一定要存在的，譬如https://s.w.org/a/b/c?d=e要被替换成https://cdn.ixnet.work/worg|/a/b/c?d=e

实际的一键实现方式可以参考[萌网的MoeCDN通用PHP类](http://git.oschina.net/kenvix/MoeCDN-Universal-PHP)

## 支持范围（可自行添加）
### 谷歌API
fonts.googleapis.com  
ajax.googleapis.com  
fonts.gstatic.com  
### WP.COM
*.wp.com  （但是没有支持JetPack的CDN，请自行添加或者关闭JetPack的CDN功能）
### Gravatar
*.gravatar.com  
### S.W.ORG
s.w.org  

## 开源协议
MIT开源
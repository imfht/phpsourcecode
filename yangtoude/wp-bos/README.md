=== wp-bos 云端图片存储 ===

Contributors:       yangtoude
Tags:               wordpress, 云存储, BOS
Requires at least:  4.5
Tested up to:       4.5
Stable tag:         trunk
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

wp-bos 云端图片存储 Wordpress 插件：将图片上传至云端，节省服务器的空间。

== Description ==

wp-bos支持使用云存储作为图片的存储空间，目前支持BOS百度云存储。
当你设置好插件的各项参数并启用后：
(1)从媒体库上传图片时图片会被自动上传至云平台，而本地的图片则会被删除；
(2)编辑文章并从外部网站(云平台以外的网站)引用图片(复制html代码中的img)，最后点击发布或更新时，图片会被自动上传至云平台，引用的图片的外部链接(src地址)会被替换为对应的云平台上的地址；
(3)在媒体库删除图片时会将云平台上的图片删除。


== Installation ==

1. 上传 `wp-bos`目录 到 `/wp-content/plugins/` 目录
2. 在后台插件菜单激活该插件
3. 在后台设置你在对应云平台的各项参数即可



== Changelog ==

= 1.0.7=
* cache bug

= 1.0.6=
* code refactor and filename conflict bug

= 1.0.5=
* get url from cdn or bucket source site

= 1.0.4=
* fix del attachements bug

= 1.0.3 =
* 初始版本

# Yii 网页播放器扩展 yii-web-player

Yii 框架网页播放器扩展，百度影音、优酷、搜狐等。

## 如何使用？

下载源码 [yii-web-player](http://git.oschina.net/dizhang/yii-web-player/repository/archive?ref=master), 解压后放到 `extensions` 文件夹下。

在 `view` 中使用如下代码:

**注意:** 百度影音视频不能使用通用播放器([VideoPlayer](http://git.oschina.net/dizhang/yii-web-player/blob/master/VideoPlayer.php)), 应使用专用的 [BaiduPlayer](http://git.oschina.net/dizhang/yii-web-player/blob/master/BaiduPlayer.php)


```php

<?php

// 播放百度影音视频
$this->widget('ext.yii-web-player.BaiduPlayer', array(
    'url'=>'bdhd://2483702575|CC27CF6C35487D693F51DFEF13C8DB9D|惊天魔道团BD1280超清中英双字无剪辑版[www.quanji.com].mkv'
));

// 播放优酷视频
$this->widget('ext.yii-web-player.VideoPlayer', array(
    'url'=>'http://player.youku.com/player.php/Type/Folder/Fid/21061779/Ob/1/sid/XNjQxMjI5MTc2/v.swf'
));

// 播放搜狐视频
$this->widget('ext.yii-web-player.VideoPlayer', array(
    'url'=>'http://share.vrs.sohu.com/1327374/v.swf&autoplay=false'
));
?>
```
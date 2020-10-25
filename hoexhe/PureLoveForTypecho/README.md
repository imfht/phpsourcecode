# PureLoveForTypecho 简介

### 演变历程

- `PureLoveForTypecho` （纯真的爱）前身是`Purelove` (`Wrodpress`主题)
- `Purelove`是梦月酱设计的[`Wrodpress`](https://cn.wordpress.org/)主题, 页面设计简洁美观 完美支持移动设备端, 支持响应式, 兼容主流游览器
- 而本主题是在[`PureLove主题梓喵出没修改版`](https://www.azimiao.com/purelovethemes)基础上再次改版, 并从`Wrodpress`移植到`typecho`上

### Demo

- [演试地址:www.hoehub.com](http://www.hoehub.com)
- [主题源码](https://gitee.com/HoeXhe/PureLoveForTypecho)

### Description

- 仿全站`Pjax` (除评论提交外)
- 自定义首页轮播图/侧边栏显示/Logo等
- 代码高亮显示
- 评论区显示系统及浏览器信息 博主标识
- 新增归档页面
- 自动获取文章第一张图片做为缩略图, 如文章无图, 则随机显示8张来自[站酷 (ZCOOL)](http://www.zcool.com.cn)的缩略图
- 设备小于`860px`时, 侧边栏和页脚会隐藏
- 页脚调用金山每日一句接口

### Use

1. `git clone https://gitee.com/HoeXhe/PureLoveForTypecho.git` 或下载主题
2. 把主题文件夹改为 `PureLoveForTypecho`, 将主题放入`/usr/themes/`目录下
3. 登录控制台使用和配置外观即可

### Link

- 主题作者梦月酱
- [梦月酱`PureLove`主题原版](https://www.mywpku.com/purelove.html)
- [`PureLove`主题梓喵出没修改版](https://www.azimiao.com/purelovethemes)

### Thanks

- 梦月酱
- [野兔](https://www.azimiao.com)
- [熊猫小A](https://blog.imalan.cn)
- [typecho社区](http://forum.typecho.org/)

### Defect

- 小尺寸设备时菜单会被截取而不是缩放
- 如使用`Pjax`提交评论后, 当前URL会有后缀`/comment` 如按`F5`刷新页面会报错
- 如使用`Pjax`提交评论后, 无法再次提交评论

### 类库

[使用七牛云CDN加速](http://www.staticfile.org)

- `jquery.js` v1.12.2
- `jquery.pjax.js` v2.0.1
- 字体图标 `font-awesome.css` v4.7.0
- 代码高亮 `highlight.js`使用`vs`样式 v9.13.1
- 进度条 `nprogress.js` v0.2.0
- 幻灯片 `responsiveslides.js` v1.55
- 输入动画库 `typed.js` v2.0.9
- 评论表情库 `emojionearea` v3.4.1
- 图片灯箱 `fancybox` v3.5.7

### 参与贡献

1. Fork 本项目
2. 新建 Feat_xxx 分支
3. 提交代码
4. 新建 Pull Request

### 许可证 License

- 本项目遵循GPL-3.0开源协议发布。
- 版权所有Copyright © 2018 by Hoe (http://www.hoehub.com)
- All rights reserved。

### 日志

- 2020-07-13 v1.5.0 留言列表可优先显示QQ头像

- 2020-07-09 静态CSS JS 使用[七牛云CDN加速](http://www.staticfile.org/)

- 2020-05-29 v1.4.0 新增图片灯箱插件 `fancybox.js`

- 2020-03-26 v1.3.0 版本新增检验JSON格式的功能

- 2019-12-20 1.取消默认项，统一在后台设置外观里配置 2.优化菜单 3.主题取消[疯狂打字机](https://www.hoehub.com/PHP/typecho-ActivatePowerMode.html)效果

- 2018-12-26 改版归档页面

- 2018-12-09 侧边栏&底部响应式显示

- 2018-12-07 评论头像如果没有定义__TYPECHO_GRAVATAR_PREFIX__, 默认使用V2EX服务器

- 2018-12-02 重置文章内容页 ul ol 样式

- 2018-12-02 防止html标签意外闭合而导致的页面布局混乱	

- 2018-11-30 解决按tab键 无法定位到评论框	

- 2018-11-29 修复文章列表标题超长	

- 2018-11-29 感谢只留下用户名的:jiffei反馈问题	

- 2018-11-26 侧边栏最近回复不显示博主评论	

- 2018-11-25 菜单&文章部分字体调整14px	

- 2018-11-22 评论表情emojionearea库

- 2018-11-23 最新回复做成图片墙

- 2018-11-20 调取金山每日一句

- 2018-11-21 评论暂时不使用Pjax
- ...

### 演示图

- 首页

    ![首页图片](demo/index.jpg)
    
- 时间轴(归档)页
    
    ![时间轴(归档)页](demo/timeline.jpg)
    
- 文章页

    ![首页图片](demo/article.jpg)
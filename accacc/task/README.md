# Montage GTD 一个基于Laravel 集RSS阅读、思维导图、番茄工作法于一体的GTD Web应用

![avatar](public/img/index.jpg)

## 快速体验
[https://task.congcong.us](https://task.congcong.us)

## 开源地址
https://gitee.com/accacc/task

## 技术栈
基于php nginx mysql composer等工具
- php: >=7.0.0
- laravel: 5.5.*
- mysql:>=5.5.*

## 功能特性

1. 番茄工作法+任务列表
- [支持] 支持引导使用功能
- [支持] 支持番茄工作法时钟自定义，找到自己最高效的时间（最小需大于10分钟）
- [支持] 完成番茄钟之后，双击待办列表即可添加该番茄钟描述
- [支持] 针对未开番茄钟完成的有意义事情进行记录
- [支持] 待办事项支持提醒功能 支持deadline之后醒目提醒
- [支持] 待办事项支持四象限来管理，即不重要不紧急、重要不紧急、紧急不重要、不紧急不重要
- [支持] 待办事项支持分目标管理任务，方便后续归纳及总结
- [即将支持] 待办事项即将支持暂时隐藏长期任务一段时间

2. 阅读
- [支持] 支持RSS订阅
- [支持] 支持拖动管理订阅排序
- [支持] 支持稍后阅读、支持加星、收藏等
- [支持] 支持分享到社交网络
- [支持] 支持语音播放某篇文章
- [即将支持] 增加针对微博、微信公众号订阅 
- [即将支持] 头条博文、个性推荐博文
- [即将支持] 每日读订阅功能

3. 思维导图
- [支持] 支持快速新增导图 支持增加描述
- [支持] 支持快捷键插入新节点、更改节点等
- [支持] 支持思维导图，导出为图片
- [即将支持] 将喜欢的文章一键生成html文章 
- [即将支持] 更高效的编辑框增加导图描述 

4. 想法
- [支持] 支持标签功能 支持公开或者私密发布
- [支持] 支持chrome等高版本浏览器上面，语音记录想法功能
- [支持] 支持分享网页到想法 自动读取网页标题
- [支持] 支持分享图片到想法
- [支持] 自动引导书写每日小目标 每日总结

5. Kindle订阅推送
- [支持] 支持将订阅推送到你的kindle设备
- [支持] 支持测试推送 支持带图推送
- [即将支持] 自定义推送特定RSS订阅项内容 

6. 统计
- [支持] 按月支持针对阅读、番茄、想法等统计的饼图与柱状图记录
- [即将支持] 更细化的番茄工作法统计，更完善的提醒

## 如何更高效的使用Montage GTD

- 快速订阅，chrome浏览器安装 [RSS Subscription Extension](https://chrome.google.com/webstore/detail/rss-subscription-extensio/nlbjncdgjeocebhnmkbbbdekmmmcbfjd) 增加订阅选项之后 点击立即订阅即可
```
录入说明: 订阅到Montage GTD
录入网址：http://task.congcong.us/feeds?url=%s
```

- 快速分享，chrome浏览器安装 [右键搜](https://chrome.google.com/webstore/detail/context-menus/phlfmkfpmphogkomddckmggcfpmfchpn)
```
右键“右键搜标识”选择选项，自定义中进行设置：
页面菜单：https://task.congcong.us/notes?add_content=%s
划词菜单：https://task.congcong.us/notes?add_content=%s
图片菜单：https://task.congcong.us/notes?type=image&add_content=%s
链接菜单：https://task.congcong.us/notes?add_content=%s
```

## 如何快速基于此部署或者进行二次开发
- fork该项目 https://gitee.com/accacc/task
- 于/database/db.sql 获取sql，进行相关表创建
- 提前安装php环境，安装相应扩展，配置nginx或者apache虚拟域名等，nginx配置可参考
- 执行composer install,即可成功访问




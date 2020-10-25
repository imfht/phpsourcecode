##拾光 管理信息系统 开发框架
基于`lumen`/`angurlarjs`/`lumx`的管理信息系统开发框架，用于快速构建项目后台管理系统

配置数据库连接 -> 初始化表配置文件 -> 配置表配置文件 -> 配置前端目录 -> 拥有强大的管理后台 -> 针对特殊需求进行开发

##情景故事
拾光团队最近在开发自己的一款App，但主要开发力量都放在了各种客户端上，团队无暇顾及后台管理系统的开发。  
通过这套开发框架，团队快速构建了一套针对此APP的管理后台，拥有了权限管理、以及强大的数据管理功能，团队只需针对某些特殊需求进行简单开发即可。

##主要功能点

###DONE
- 对于任意mysql数据表的基本的增删改查操作
- 索引时根据字段关联进行关联查找、显示
- 创建修改时对关联字段进行实时搜索
- 分页
- 排序
- 文件上传
- 自定义字段
- 快捷导航
- 用户登录

###TODO

- 高级搜索
- 日期选择
- 格式验证
- 角色、权限配置

###BUGLIST

- 操作过快导致实体id丢失

##部署

###服务器环境

lumen开发环境 [lumen DOC](http://lumen.laravel.com/docs/installation#installation) 

- PHP >= 5.5.9
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension

###前端环境

Chrome/IE11

###源码下载

```
git clone https://git.oschina.net/hillsdong/ptimemis.git
```

###测试数据

[ptimecms data](http://git.oschina.net/yantu/ptimecms/raw/master/ptimecms.sql)

###配置

1. 编辑 `/.env` 文件，进行数据库配置，只支持mysql  
2. 访问 `http://{root_url}/config/init`，自动在`/storage/app/config`生成针对各数据表的配置文件
3. 编辑 `/storage/app/config` 中需要进行配置的表
3. 编辑 `/public/config.js` 中配置
3. 替换 `/public/favicon.png`
3. 修改 `/public/css/app.css` 中主色
3. 访问 `http://{root_url}`

###注意事项

- 数据表须有id做为唯一主键

##配置项说明

##API接口说明

##截图
![列表页](http://static.oschina.net/uploads/space/2015/0925/101609_w8yP_1160948.jpg "列表页")
![详情页](http://static.oschina.net/uploads/space/2015/0925/101644_XhvB_1160948.jpg "详情页")

# nCov-Crawler

## 介绍

基于 Swoole 的新型冠状病毒肺炎疫情实时动态爬虫抓取项目

采集数据来源：<https://3g.dxy.cn/newh5/view/pneumonia_peopleapp>

目前采集持久化了：统计总数据、省统计、市统计、外国统计

演示地址：<https://test.yurunsoft.com/ncov/>

## 软件架构

### 爬虫、接口

使用基于 Swoole 的 imi 框架开发 <https://www.imiphp.com/>

imi 框架交流群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)

从秃头到满头秀发的 imi 框架教程，全集免费观看👉<https://www.bilibili.com/video/av78158909>

imi-cron 定时采集任务 <https://doc.imiphp.com/components/task/cron.html>

YurunHttp 网络请求包 <https://gitee.com/yurunsoft/YurunHttp>

正则表达式截取数据

### 数据展示

Vue + Vux

## 安装教程

### 爬虫、接口

首先需要安装 Swoole 环境：<https://wiki.swoole.com/wiki/page/6.html>

切换到 `src` 目录，执行命令：`composer update`

导入 `db_ncov.sql` 文件到 MySQL

修改 `src/config/config.php` 文件中的数据库相关配置：

```php
'resource'    =>    [
    'host'        => '127.0.0.1',
    'port'        => 3306,
    'username'    => 'root',
    'password'    => 'root',
    'database'    => 'db_ncov',
    'charset'     => 'utf8mb4',
    'options'   =>  [
        \PDO::ATTR_STRINGIFY_FETCHES    =>  false,
        \PDO::ATTR_EMULATE_PREPARES     =>  false,
    ],
],
```

### 数据展示

目录：`src/web`

安装：`npm install`

前往 `src/web` 中的 `.env`、`.env.development` 文件中修改接口地址

开发调试：`vue-cli-service serve`

构建静态页：`vue-cli-service build`

静态页构建完成后，需要添加 nginx 代理

## 使用说明

切换到 `src` 目录，执行命令：`vendor/bin/imi server/start`

默认 3 分钟采集一次，如需修改请到文件：`src/Module/Crawler/Cron/CrawlerTask.php`

## 参与贡献

1. Fork 本仓库
2. 新建 Feat_xxx 分支
3. 提交代码
4. 新建 Pull Request

## api 接口

### /api/statistics

统计数据接口

演示地址：<https://test.yurunsoft.com/ncov/api/statistics>

### /api/areas

地区数据接口

参数：

| 名称 | 必传 | 说明 |
| - | - | - |
| city |  | 是否包含子城市数据，1-包含，0-不包含。缺省时为1 |

演示地址：

<https://test.yurunsoft.com/ncov/api/areas>

<https://test.yurunsoft.com/ncov/api/areas?city=0>

### /api/statisticsDateSpan

日期区间查询统计数据接口

演示地址：<https://test.yurunsoft.com/ncov/api/statisticsDateSpan?beginDate=2020-01-28&endDate=2020-02-29>

参数：

| 名称 | 必传 | 说明 |
| - | - | - |
| beginDate | √ | 开始日期 |
| endDate | √ | 结束日期 |

### /api/areasDateSpan

地区日期区间查询统计数据接口

演示地址：<https://test.yurunsoft.com/ncov/api/areasDateSpan?countryType=1&provinceName=%E6%B1%9F%E8%8B%8F%E7%9C%81&beginDate=2020-01-28&endDate=2020-02-29>

参数：

| 名称 | 必传 | 说明 |
| - | - | - |
| countryType | √ | 国家类型；1-中国；2-外国 |
| provinceName | √ | 地区名称（省名称） |
| beginDate | √ | 开始日期 |
| endDate | √ | 结束日期 |

### /api/cityDateSpan

城市日期区间查询统计数据接口

演示地址：<https://test.yurunsoft.com/ncov/api/cityDateSpan?parentId=34&cityName=无锡&beginDate=2020-01-28&endDate=2020-02-29>

参数：

| 名称 | 必传 | 说明 |
| - | - | - |
| parentId | √ | 父级ID（自编） |
| cityName | √ | 城市名称 |
| beginDate | √ | 开始日期 |
| endDate | √ | 结束日期 |

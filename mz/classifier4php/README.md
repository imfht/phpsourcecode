### classifier4php

基于 PHP 和 word2vec 的简单分类器，用于文章、新闻等内容自动分类，项目包含样本训练、识别代码，

分词组件用的是 PhpAnalysis，简单灵活。欢迎大家一起优化并完善。 


项目地址：

码云: [https://gitee.com/mz/classifier4php](https://gitee.com/mz/classifier4php)

Github: [https://github.com/djunny/classifier4php](https://github.com/djunny/classifier4php)

### 背景

每个搜索引擎其实都有一套完善的分类器，拿最简单的分类器举例，
不管你是巨头门户还是垂直三、四级以下的网站，他都能识别你的站点类型。
面向海量内容的今天，随随便便就能从互联网采集、抓取海量的数据，
而数据又杂乱无章，如果用人工整理归类，太浪费资源了。

作者做过各类站群、垂直站点，深知分类器的重要性。

### 运行环境

1. 操作系统：windows \ *inux
2. PHP 版本：PHP 5+
3. PHP 依赖：PHP-mbstring.
4. word2vec：window xp

如果您的操作系统是Linux、Centos等，

您需要自行下载 word2vec ( https://code.google.com/p/word2vec/ )编译。

然后修改 run.php 中 word2vec 执行路径:

```
define('EXE_WORD2VEC', 'word2vec.exe');
```

系统自带了基于 windows 的 word2vec 版本。


### 项目实例1: demo1/run.php

项目中写了一个将小说自动训练并归类为：现代和古代的例子。

训练集结果文件已经存在于 source_data 目录中。

您可以直接将要识别的小说文件放至 source_target 中，即可自动识别。


## 运行方式
 
配置 PHP 路径到系统环境变量 PATH 中，或者手工执行：

/path/php run.php > run.log

即可在 run.log 中看到运行结果。

注：windows 下，设置好 PATH 后，也可以直接运行 run.bat

### 项目实例2: demo2/index.php

请用浏览器访问，截图：

![截图1](https://gitee.com/mz/classifier4php/raw/master/screen_1.png "截图1")

![截图2](https://gitee.com/mz/classifier4php/raw/master/screen_2.png "截图2")


本实例是经过千万数据集训练出来的结果，

可以直接用于生产环境下的新闻分类，支持自动分类以下常见新闻类型：

```
财经
-保险
-产经
-宏观
-基金
-理财
-企业
-新股
-银行
-证券

房产
-八卦
-明星
-政策
-专家
-资讯

国际

国内

军事
-港澳台
-观察
-国际
-国内
-科技
-秘史
-评论

科技
-IT
-互联网
-家电
-酷玩
-软件
-数码
-探索
-通信

历史
-解密
-人物
-文史
-野史
-战史

旅游
-发现
-攻略

美食

女人
-彩妆
-丰胸
-护肤
-香水
-整形

汽车
-厂商
-访谈
-媒体
-资讯

社会
-法律
-奇闻
-万象

时尚
-街拍
-视觉
-资讯

体育

养生
-按摩
-保健
-减肥
-美容
-营养
-中药

游戏
-攻略
-海外
-人物
-周边
-资讯

育儿
-宝宝健康
-宝宝营养
-备育
-产后
-明星育儿
-母婴
-曝光
-幼儿园
-游戏
-育期
-资讯

娱乐

政务
-部委
```

可通过浏览器访问 demo2/index.php



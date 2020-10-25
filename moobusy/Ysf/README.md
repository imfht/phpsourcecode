#Ysf PHP Framework

####PHP5.5 +

####模板引擎使用smarty

####数据库连接使用更加安全的PDO

还在逐步完成.:bowtie:

##近期任务

- :star2: :star2:增加memcache，redis
- :star2:完善route

##目标:
* 不过度封装
* 功能单一化
* 扩展热拔插

##目录结构
    app                   默认项目主体目录
        config            项目配置
        controller        控制器
        model             数据模型
        view              模板
    Ysf                   推荐框架目录/框架主体
        Conf              框架配置
        Function          框架函数
        Library           框架类库
        Vendor            框架扩展
        Ysf.php           框架入口
    public                推荐项目前端目录
        index.php         项目入口
        static            静态文件
        upload            上传文件
    runtime               默认缓存目录
        cache             项目文件缓存
        log               项目日志
        template          项目模板缓存
##入口文件
```
<?php 
define('APP_MODE','DEV');
define('PUBLIC_PATH',dirname(__FILE__));
define('TOP_PATH',dirname(PUBLIC_PATH));

include_once '../Ysf/Ysf.php';
?>
```
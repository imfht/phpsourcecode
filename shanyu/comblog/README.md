## 使用说明
0. 下载源代码压缩包  
[https://gitee.com/shanyu/comblog/repository/archive/master.zip](https://gitee.com/shanyu/comblog/repository/archive/master.zip)

1. 修改`.env`的数据库配置
    ```
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=blog
    DB_USERNAME=root
    DB_PASSWORD=root
    ```
2. 修改`.env`的管理后台账号密码
    ```
    ADMIN_USER=admin
    ADMIN_PASS=admin
    ```
3. 给予缓存目录写入权限
    ```
    chomd -R 775 runtime
    ```
4. 导入`Mysql`数据结构和测试数据`mysql_database.sql`  
5. 进入后台添加文章`/admin`

## NGINX伪静态
```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 模板修改
* 模板目录 `template`
* 关于博主页面 `template\page_about.php`

## 网站预览
文章详情  
![文章详情](https://git.oschina.net/uploads/images/2017/0625/153415_c181cc5a_10167.jpeg "文章详情")  
编辑文章  
![编辑文章](https://git.oschina.net/uploads/images/2017/0625/153443_be3631cb_10167.jpeg "编辑文章")  

## 扫码领红包
![扫码领红包](https://images.gitee.com/uploads/images/2018/1216/012029_3a2ccf3b_10167.jpeg "扫码领红包")
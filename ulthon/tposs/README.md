# tposs
tp observe storage service
基于tp5的观察存储服务。(不是对象存储服务,改成这个了.是我太年轻,小看了oss.)

## 目标
做成成熟的oss系统,支持分布式.

## 当前
只做了file模块,能够操作本地文件(还不是oss,只是顶多算web_file_system,能通过http接口操作文件)

## todo
增加搜索,图片预览,根据格式列举,远程解压,流视频等功能,做重命名功能;

## 使用
搭建tp5网站,确保使用web站点,能隐藏index.php

    应用目录下config有个配置access_key,访问必须带这个参数且一直才能访问

文件上传:http://oss.com/upload/sadf/sadf/sda

文件删除:http://oss.com/delete/sadf/sadf/sd/f

获取列表:http://oss.com/list/sadf/sadf/sda

目录信息:http://oss.com/info/

主机后第一个参数是操作(upload,list,info,delete),后面的是文件路径,其中.获取列表和目录信息可使用post.base_dir参数指定

#### 安装
    修改application/config.php.
    修改两个配置项:
    'host_name'=>'http://oss.com',
    'access_key' => 'asdfsadfsda'

#### 基本使用
    配置access_key参数,上传,列表,删除,或许信息接口必须提交form.post.access_key属性,且值与配置项相同,否则退出程序.
    配置host_name参数,上传或列表时,会把资源拼接成完整url链接返回到json
    注意,每次请求都至少有一个base_dir参数,也可以忽略这个参数,在url里操作,比如下面两种方式是等价的.

    上传a.jpg到b目录:
    可以通过post请求http://oss.com/upload
    并带有post.access_key,
    post.base_dir = '/b/a.jpg'
    以上写法等价于
    http://oss.com/upload/d/a.jpg
    并带有post.access_key,

----

#### upload
    上传文件,主机名后第一个参数为upload,即上传文件
    如果最后以"/"结尾则创建目录,忽略上传的文件
    如果最后以字符串结尾,那么上传文件

    例1:
    上传图片,
    上传接口为http://oss.com/upload
    在form中添加base_dir参数:/a/b/c/d.jpg
    在form中添加file参数:本地文件
    在form中添加access_key参数:配置的通行码
    提交form,如果不存在a,b,c相关目录,会创建相关目录,并将d.jpg放到该目录下,
    成功后返回,其中form中base_dir可以用这种形式代替:http://oss.com/upload/a/b/c/d.jpg
```
{
    "file_path": "./data/a/b/c",
    "file_name": "d.jpg",
    "base_dir": "./data/a/b/c/d.jpg",
    "code": 200,
    "msg": "上传文件成功",
    "data": {
        "getExtension": "jpg",
        "getSaveName": "d.jpg",
        "getFilename": "d.jpg",
        "url": "http://oss.com/data/a/b/c/d.jpg"
    }
}
````

    例2:
    新建目录,
    当base_dir以斜线结尾时,创建目录,忽略上传的文件
    上传接口为http://oss.com/upload
    在form中添加base_dir参数:/a/b/c/e/
    在form中添加file参数:可为空
    在form中添加access_key参数:配置的通行码
    提交form,如果不存在a,b,c相关目录,会创建相关目录,并将d.jpg放到该目录下,
    成功后返回,其中form中base_dir可以用这种形式代替:http://oss.com/upload/a/b/c/e/
```
{
    "file_path": "./data/a/b/c/e",
    "file_name": "",
    "base_dir": "./data/a/b/c/e/",
    "code": 200,
    "msg": "创建目录成功"
}
````

    其他情况:
    假设以上两个例子正常执行,此时调用接口http://oss.com/upload/a/b/c/d.jpg/
    并填写相关参数,此时会报错,这个链接是创建d.jpg的目录,但实际上相同位置已经存在一个文件名相同,不能创建.
    同理,调用 http://oss.com/upload/a/b/c/e 也会失败,因为相同位置已经存在一个目录名为e.
    

----

#### delete
    删除文件和目录,
    如果最后以"/"结尾,那么操作目录,接受post.rm参数为true时,强制删除指定目录
    如果以字符串结尾,那么删除文件


    例子:删除目录
    http://oss.com/delete/a/b/c/e/
    form带上access_key参数.
    正常会返回以下结果.
    如果目录不存在会提示目录不存在.
```
{
    "file_path": "./data/a/b/c/e",
    "file_name": "",
    "base_dir": "./data/a/b/c/e/",
    "code": 200,
    "msg": "删除目录成功"
}
```

    例子:删除文件
    http://oss.com/delete/a/b/c/d.jpg
    form带上access_key参数.
    正常会返回以下结果.
    如果目录不存在会提示文件不存在.
```
{
    "file_path": "./data/a/b/c",
    "file_name": "d.jpg",
    "base_dir": "./data/a/b/c/d.jpg",
    "code": 200,
    "msg": "删除成功"
}
```


    其他情况:
    强制删除目录:
    某个目录下有文件,那不能删除目录,可以添加post.rm=true参数强制删除目录下面文件
    即

    请求地址:http://oss.com/delete
|参数|值|
|:---|:---|
|base_dir|/a/b/c/|
|rm|true|
|access_key|配置访问码|

    以上方式等价于(推荐这种方式)
    请求地址:http://oss.com/delete/a/b/c/
|参数|值|
|:---|:---|
|rm|true|
|access_key|配置访问码|


----


#### list
    列举目录,不论是否以"/"结尾,都操作目录
    接收参数
    post.l:显示详情
    post.h:格式化文件大小显示

    访问:http://oss.com/list/a并带有post.access_key可以列举a目录下的文件,其中不论是否以"/"结尾,都会列举a目录的列表,不存在则提示.

    一般显示如下结果:
```
{
    "file_path": "./data/a",
    "file_name": "",
    "base_dir": "./data/a/",
    "code": 200,
    "msg": "",
    "data": [
        "b"
    ]
}
```

    如果带有以下参数,则返回详细内容

    访问http://oss.com/list/a
|参数|值|意义|
|:--|:--|:--|
|l|true|是否显示详情|
|h|true|是否人性化显示,字节数转mb|
|access_key|配置访问码|

```
{
    "file_path": "./data/a",
    "file_name": "",
    "base_dir": "./data/a/",
    "code": 200,
    "msg": "",
    "data": {
        "b": {
            "name": "b",
            "is_writeable": true,
            "is_executable": false,
            "is_readable": true,
            "realpath": "D:\\phpStudy\\WWW\\tposs\\public\\data\\a\\b",
            "url": "http://oss.com/data/a//b",
            "is_dir": true,
            "size": 0
        }
    }
}
```

    访问http://oss.com/info/a
|参数|值|意义|
|:--|:--|:---|
|ra|true|是否递归计算所有子目录和文件|
|h|true|是否人性化显示,字节数转kb,mb|
|access_key|配置访问码|

#### info 
    获得目录基本信息
    post.ra:递归读取目录下所有文件和目录
    post.h:文件大小格式化



```
{
    "file_path": "./data/a",
    "file_name": "",
    "base_dir": "./data/a/",
    "code": 200,
    "msg": "",
    "data": {
        "disk_free_space": "99.562 GB",
        "disk_total_space": "292.969 GB",
        "files": {
            "file_count": 0,
            "dir_count": 1,
            "count": 1
        },
        "all_files": {
            "file_count": 0,
            "dir_count": 0,
            "count": 0
        },
        "is_writeable": true,
        "is_executable": false,
        "is_readable": true,
        "realpath": "D:\\phpStudy\\WWW\\tposs\\public\\data\\a",
        "url": "http://oss.com/data/a/"
    }

```

#### data
    主机名/data/资源路径
    直接是资源路径,需要关闭debug

    所有操作都会返回url结果,该链接即访问路径,
    注意,如果资源不存在会尝试进入data模块,需要把debug关掉.
    
    注意:默认服务器不允许读取目录列表,也不建议开启该功能.
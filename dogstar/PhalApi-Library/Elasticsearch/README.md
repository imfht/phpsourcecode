#基于PhalApi的ELASTICSEARCH操作类
[elasticsearch官方api文档](http://www.elastic.co/guide/en/elasticsearch/reference/current/docs.html)

### 1.安装和配置

#### 1.1 扩展包下载
从 PhalApi-Library 扩展库中下载获取 Elasticsearch 扩展包，如使用：

git clone https://git.oschina.net/dogstar/PhalApi-Library.git
然后把 Elasticsearch 目录复制到 ./PhalApi/Library/ 下，即：

cp ./PhalApi-Library/Elasticsearch/ ./PhalApi/Library/ -R
到处安装完毕！接下是插件的配置。

#### 1.2 扩展包配置
我们需要在 ./Config/app.php 配置文件中追加以下配置：
##### 1.2.1 服务配置
```
    /**
     * Elasticsearch服务相关配置
     */
    'DB_CONFIG_ELASTICSEARCH' => array(
        //对应的文件路径
        'DB_HOST' => 'your DB_HOST',//es服务ip
        'DB_PORT' => 'your DB_PORT',//es服务端口
        'DB_INDEX' => 'your DB_INDEX',  //默认index 通过switchIndex 切换
        'DB_TABLE' => 'your DB_TABLE',//默认table 通过switchTable 切换
    ),
```


### 2.入门使用
#### 2.1 入口注册
```
$loader->addDirs('Library');

//其他代码...

//初始化(使用配置)
DI()->es = new Elasticsearch_Lite();


//初始化(传参)
DI()->es = new Elasticsearch_Lite('192.168.14.110','9200','account','user');

```
![入口注册](https://ws1.sinaimg.cn/large/006tKfTcgy1fqpu4ac1hej315g0pijw1.jpg)

### 3.示例
先简单写个查询示例：
```
        $data = [
            "query" => [
                "match" => [
                    "userPhone" => "13800138000"
                ]
            ]
        ];

        return DI()->es->search($data);
```
![示例](https://ws4.sinaimg.cn/large/006tKfTcgy1fqpu2a8gwxj30sg0pitbh.jpg)
 
### 4. 实例可用方法

| method    | 说明    |
| ----------- | ----------- |
| createIndex | 创建索引 |
| createMapping | 创建字段 |
| switchIndex | 切换索引 |
| switchTable | 切换table |
| add | 添加数据 |
| search | 查找数据 |
| delete | 删除数据 |
| view | 查看 |


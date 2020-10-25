# JsonDB

#### 项目介绍
JsonDB

是一个由原生PHP实现的文件数据库，JsonDB只有一个文件，如果你不想使用庞大的数据库系统，而且只需要单机功能，那么JsonDB就是你最佳的选择。
JsonDB只有初始化写入和查询功能,所以适用于比较固定的,数据量不大的数据,比如地区.

帮助使用文档:https://gitee.com/web/JsonDB/wikis

#### 软件架构
由纯原生PHP实现的json文件数据库,将数据存储为json格式,不占用mysql资源纯以读写文件的形式查询数据库,写法类似于thinkphp的查询.支持压缩存储json大大减少空间占用



#### 使用说明


```php
<?php
header("content-Type: text/html; charset=utf-8");
include('JsonDB.class.php');
$db = new JsonDB('areazip');
$param=array();
$param['pid']=130;
$param['id']=130;
$param['_logic']='or';
$area=$db->select($param);

$param=array();
$param['name']=array('like','河北');
$area=$db->select($param,1);//增加limit参数 提高效率

$param=array();
$param['id']=130;
$areaname=$db->find($param,'name');
?>
```
支持查询语法:eq,neq,like,in,notin,gt,lt,egt,elt 可自行扩展

_logic 定义 and 或 or

#### 案例
https://gitee.com/web/mobileLocation

#### 效率测试
几位同学对效率问题提出质疑,以简单的在本地测试了一下不知道合不合理下面是代码

```php
$startTime = microtime(true);
$array=array();
for($i=1; $i<=100000; $i++){
    $array[]=array("id"=>$i,"name"=>"name".$i,"pinyin"=>$i,"pid"=>"0","status"=>"0","sort"=>"0","temp"=>"","letter"=>"\ufeffZ","level"=>"0","region"=>"0");
}
$area=$db->add($array,1);
$endTime = microtime(true);
echo '添加成功，耗时： ' .(($endTime - $startTime)*1000) . 'ms';
//耗时： 597.57590293884ms  612.07795143127ms

$startTime = microtime(true);
$param=array();
$param['id']=100000;
$area=$db->select($param);
print_r($area);
$endTime = microtime(true);
echo '查询成功，耗时： ' .(($endTime - $startTime)*1000) . 'ms';
//耗时： 1074.1360187531ms

```
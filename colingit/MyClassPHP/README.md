# MyClassPHP
MyClassPHP是一个开源、免费的学习框架。官方交流群 [438695935](https://shang.qq.com/wpa/qunwpa?idkey=1331030787e315dd0026359c55c757b439562acd0f1ee51855b709faf0e4652d)

## 在线文档
[传送](https://www.kancloud.cn/amcolin/myclassphp_3_2_0/1325215)

## 更新日志
[查看](https://github.com/a1586256143/core/blob/master/UPDATE.md)


## 安装、使用
安装框架
```
git clone https://github.com/a1586256143/MyClassPHP.git
```
下拉composer所需组件
```
composer install
```

例如：在controllers 建立Index.php，代码如下
```
namespace controllers;
use system\Base;
class Hello extends Base{
    public function index(){
        return 'Hello MyClassPHP';
    }
}
```
打开 config/routes.php，追加一条路由
```
'/hello' => 'Hello@index'
```
配置完成如下
```
Route::add(array(
    '/' => 'Index@index' , 
    '/hello' => 'Hello@index'
))
```

运行
```
http://域名/hello
```
